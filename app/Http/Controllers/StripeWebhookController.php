<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SupportPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        // 1. Setup Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        // 2. Verify Signature
        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe signature verification failed.');
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook error'], 400);
        }

        // 3. Handle Events
        switch ($event->type) {

            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSession($session);
                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $this->handleInvoicePaid($invoice);
                break;

            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;

            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $this->handleInvoiceFailed($invoice);
                break;



            default:
            // Log unknown events just for debugging (optional)
            // Log::info('Received unknown event type ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }


    protected function handlePaymentIntentSucceeded($paymentIntent)
{
    // Retrieve metadata you attached in createSubscription
    $userId = $paymentIntent->metadata->user_id ?? null;
    $planId = $paymentIntent->metadata->plan_id ?? null;
    $interval = $paymentIntent->metadata->interval ?? 'monthly';

    if ($userId) {
        $user = User::find($userId);
        
        if ($user) {
            $expiresAt = ($interval === 'quarterly') 
                ? now()->addMonths(3) 
                : now()->addMonth();

            $user->update([
                'has_paid' => true,
                'payment_status' => 'paid',
                'stripe_subscription_id' => $paymentIntent->id,
                'subscription_plan' => $planId,
                'stripe_customer_id' => $paymentIntent->customer,
                'payment_date' => now(),
                'payment_expires_at' => $expiresAt,
            ]);

            Log::info("Custom PaymentIntent succeeded for User {$userId}");
        }
    }
}

    /**
     * Handle One-Time Checkout Sessions
     */
    protected function handleCheckoutSession($session)
    {
        // Get metadata we passed in startCheckout()
        $userId = $session->metadata->user_id ?? null;
        $planId = $session->metadata->plan_id ?? null;
        $interval = $session->metadata->interval ?? 'monthly';



        if ($userId) {
            $user = User::find($userId);

            if ($user) {
                // Calculate expiry for one-time access
                $expiresAt = ($interval === 'quarterly')
                    ? now()->addMonths(3)
                    : now()->addMonth();

                $user->update([
                    'has_paid' => true,
                    'payment_status' => 'paid',
                    'stripe_subscription_id' => $session->payment_intent,
                    'subscription_plan' => $planId,
                    'stripe_customer_id' => $session->customer,
                    'payment_date' => now(),
                    'payment_expires_at' => $expiresAt,
                ]);

                Log::info("Checkout successful for User ID: {$userId}");
            }
        }
    }

    /**
     * Handle Recurring Support (Invoice Paid)
     * This fires for the first payment AND every month/year after.
     */
    protected function handleInvoicePaid($invoice)
    {
        // 1. Check if this invoice belongs to a subscription
        $subscriptionId = $invoice->subscription;
        if (!$subscriptionId)
            return;
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        try {
            $subscription = $stripe->subscriptions->retrieve($subscriptionId);
        } catch (\Exception $e) {
            Log::error("Could not retrieve subscription: {$subscriptionId}");
            return;
        }

        $userId = $subscription->metadata->user_id ?? null;
        $type = $subscription->metadata->type ?? null;

        if ($userId && $type === 'recurring_support') {

            $amount = $subscription->metadata->amount ?? ($invoice->amount_paid / 100);
            $interval = $subscription->metadata->interval ?? 'monthly';

            SupportPayment::updateOrCreate(
                ['subscription_id' => $subscriptionId],
                [
                    'user_id' => $userId,
                    'stripe_customer_id' => $invoice->customer,
                    'amount' => $amount,
                    'type' => 'recurring',
                    'interval' => $interval,
                    'status' => 'active',
                    'payment_intent_id' => $invoice->payment_intent,
                    'paid_at' => now(),
                ]
            );

            // OPTIONAL: Also update the User model if you want them to have "active" status globally
            /*
            User::where('id', $userId)->update([
                'payment_status' => 'active',
                'has_paid' => true
            ]);  acct_1JZM1aF5gg5CstWA
            */

            Log::info("Recurring support payment processed for User {$userId}");
        }
    }

    /**
     * Handle Failed Recurring Payments
     */
    protected function handleInvoiceFailed($invoice)
    {
        $subscriptionId = $invoice->subscription;

        if ($subscriptionId) {
            SupportPayment::where('subscription_id', $subscriptionId)->update([
                'status' => 'past_due',
            ]);

            Log::warning("Payment failed for Subscription {$subscriptionId}");
        }
    }
}
