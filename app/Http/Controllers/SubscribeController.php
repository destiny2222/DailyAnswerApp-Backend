<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\SubscriptionPlan;
use App\Models\SupportPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use RealRashid\SweetAlert\Facades\Alert;


class SubscribeController extends Controller
{



    public function index()
    {
        $subscriptions = SubscriptionPlan::all();
        return view('frontend.subscribe', compact('subscriptions'));
    }



    public function startCheckout(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            Alert::error('Error', 'User with the provided email does not exist.');
            return back();
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);


        try {
            // 2. Create Session
            $session = CheckoutSession::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'customer' => $user->stripe_customer_id,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $plan->name ?? 'Subscription',
                            ],
                            'unit_amount' => $plan->price * 100,

                        ],
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'interval' => $plan->interval,
                ],
                'success_url' => route('billing.success'),
                'cancel_url' => route('billing.cancel'),
            ]);

            return redirect()->away($session->url);

        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            Alert::error('Error', 'Unable to start checkout. Please try again.');
            return back();
        }
    }

    public function success(Request $request)
    {
        return view('frontend.billing-success');
    }

    public function cancel()
    {
        return view('frontend.billing-cancel');
    }

    protected function handleSubscriptionPaid($session)
    {
        $userId = $session->metadata->user_id ?? null;
        $planId = $session->metadata->plan_id ?? null;
        $planId = $paymentIntent->metadata->plan_id ?? 'plan_standard_quarterly';
        $interval = $paymentIntent->metadata->interval ?? 'quarterly';

        if (!$userId)
            return;

        $user = User::find($userId);

        if ($user) {

            $expiresAt = $interval === 'quarterly'
                    ? now()->addMonths(3)
                    : now()->addMonth();


            $user->update([
                'has_paid' => true,
                'payment_status' => 'paid',
                'stripe_subscription_id' => $session->payment_intent,
                 'subscription_plan' => $planId,
                 'stripe_customer_id' => $session->customer->id,
                'payment_date' => now(),
                'payment_expires_at' => $expiresAt,
            ]);

            Log::info("One-time payment successful for User {$userId}");
        }
    }


    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Retrieve the PaymentIntent from Stripe
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment has not been completed.',
                ], 400);
            }

            $user = $request->user();
            $paymentType = $paymentIntent->metadata->type ?? 'subscription';

            // Handle different payment types
            if ($paymentType === 'subscription') {
                // Get plan details from metadata
                $planId = $paymentIntent->metadata->plan_id ?? 'plan_standard_quarterly';
                $interval = $paymentIntent->metadata->interval ?? 'quarterly';

                // Calculate expiration based on plan
                $expiresAt = $interval === 'quarterly'
                    ? now()->addMonths(3)
                    : now()->addMonth();

                // Update user payment status
                $user->update([
                    'has_paid' => true,
                    'payment_status' => 'active',
                    'payment_date' => now(),
                    'payment_expires_at' => $expiresAt,
                    'stripe_customer_id' => $paymentIntent->customer,
                    'subscription_plan' => $planId,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Subscription activated successfully.',
                    'data' => [
                        'has_paid' => true,
                        'payment_status' => $user->payment_status,
                        'subscription_plan' => $user->subscription_plan,
                        'payment_date' => $user->payment_date,
                        'payment_expires_at' => $user->payment_expires_at,
                    ],
                ]);

            } elseif ($paymentType === 'one_time_support') {
                // Log one-time support payment
                $amount = $paymentIntent->metadata->amount ?? 0;

                // You can create a Support model to track these
                SupportPayment::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'one_time',
                    'payment_intent_id' => $paymentIntent->id,
                    'paid_at' => now(),
                ]);

                return back()->with([
                    'success' => true,
                    'message' => 'Thank you for your support!',
                    'data' => [
                        'type' => 'one_time_support',
                        'amount' => $amount,
                        'payment_date' => now(),
                    ],
                ]);

            } elseif ($paymentType === 'recurring_support') {
                // Recurring support is handled via Stripe Subscriptions
                $amount = $paymentIntent->metadata->amount ?? 0;

                return back()->with([
                    'success' => true,
                    'message' => 'Thank you for your recurring support!',
                    'data' => [
                        'type' => 'recurring_support',
                        'amount' => $amount,
                        'payment_date' => now(),
                    ],
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error confirming payment: ' . $e->getMessage());
            Alert::error('Error', 'Error confirming payment: ' . $e->getMessage());
            return back();
        }
    }
}
