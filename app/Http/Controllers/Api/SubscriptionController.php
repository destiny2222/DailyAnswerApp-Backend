<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Imdhemy\Purchases\Facades\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Verify the receipt sent from the React Native app.
     */
    public function verifyReceipt(Request $request)
    {
        $request->validate([
            'receipt'  => 'required|string',
            'platform' => 'required|in:ios,android',
            'product_id' => 'required|string',
        ]);

        $user = $request->user();
        $receiptToken = $request->input('receipt');
        $platform = $request->input('platform');
        $productId = $request->input('product_id');

        try {
            if ($platform === 'ios') {
                // Verify Apple Receipt
                // The third parameter 'false' means we are using the sandbox environment.
                // Switch to 'true' for production.
                $receipt = Subscription::appStore()->receiptData($receiptToken)->verifyReceipt(false);
                
                // Get the most recent active subscription from the receipt
                $latestReceiptInfo = $receipt->getLatestReceiptInfo();
                
                // Sort to find the latest
                $latestTransaction = collect($latestReceiptInfo)->sortByDesc('expires_date_ms')->first();

                if (!$latestTransaction) {
                    return response()->json(['success' => false, 'message' => 'No subscription found'], 400);
                }

                $expirationDateMs = $latestTransaction->getExpiresDateMs();
                $expirationDate = Carbon::createFromTimestampMs($expirationDateMs);

                if ($expirationDate->isPast()) {
                    return response()->json(['success' => false, 'message' => 'Subscription has expired'], 400);
                }

                // Update the user's subscription status in the database
                // (Assuming user model has is_premium and subscription_expires_at, adjust as needed)
                $user->update([
                    'is_premium' => true,
                    'subscription_expires_at' => $expirationDate,
                ]);

                return response()->json(['success' => true, 'message' => 'Subscription verified successfully!']);

            } else {
                // Verify Google Play Receipt
                // Note: You must configure your Google Play Service Account JSON credentials in config/purchases.php
                $subscription = Subscription::googlePlay()
                    ->id($productId)
                    ->token($receiptToken)
                    ->get();

                // Check if the subscription is active
                if ($subscription->getExpiryTimeMillis() < now()->timestamp * 1000) {
                     return response()->json(['success' => false, 'message' => 'Subscription has expired'], 400);
                }

                $expirationDate = Carbon::createFromTimestampMs($subscription->getExpiryTimeMillis());

                $user->update([
                    'is_premium' => true,
                    'subscription_expires_at' => $expirationDate,
                ]);

                return response()->json(['success' => true, 'message' => 'Subscription verified successfully!']);
            }

        } catch (\Exception $e) {
            Log::error('Receipt verification failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Verification failed'], 500);
        }
    }
}
