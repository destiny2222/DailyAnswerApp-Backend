<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\SubscriptionPlan;
use App\Models\SupportPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Subscription;

class PaymentController extends Controller
{

    /**
     * Get available support plans.
     */

    public function getRecurringSupportPlans()
    {
        $user = request()->user();
        $supports = SupportPayment::where('user_id', $user->id)->get();


        return response()->json([
            'success' => true,
            'plans' => $supports,
        ]);
    }


    /**
     * Get available subscription plans.
     */
    public function getSubscriptionPlans()
    {
        $subscriptions = SubscriptionPlan::all();

        return response()->json([
            'success' => true,
            'plans' => $subscriptions,
        ]);
    }

    /**
     * Create a subscription payment intent.
     */
    public function createSubscription(CreateSubscriptionRequest $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $user = $request->user();

        $plan = $request->plan_id;
        $plan = SubscriptionPlan::where('id', $plan)->first();
        if (!$plan) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid subscription plan selected.',
            ], 400);
        }

        try {
            // Create or retrieve Stripe customer
            if (!$user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ]);

                $user->update(['stripe_customer_id' => $customer->id]);
            } else {
                $customer = Customer::retrieve($user->stripe_customer_id);
            }

            // Create the PaymentIntent for subscription
            $paymentIntent = PaymentIntent::create([
                'amount' => $plan->price * 100, // amount in cents
                'currency' => 'usd',
                'customer' => $customer->id,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->plan_id,
                    'interval' => $plan->interval,
                ],
            ]);

            return response()->json([
                'success' => true,
                'clientSecret' => $paymentIntent->client_secret,
                'customerId' => $customer->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while creating the subscription.',
            ], 500);
        }
    }

    /**
     * Create a support payment (one-time or recurring).
     */
    public function createSupport(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:999999',
            'is_recurring' => 'required|boolean',
            'interval' => 'required_if:is_recurring,true|in:monthly,yearly',
        ]);

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $user = $request->user();
        $amount = $request->amount;
        $isRecurring = $request->is_recurring;
        $interval = $request->interval ?? null;

        try {

            // Create or get existing customer
            if ($user->stripe_customer_id) {
                $customer = $stripe->customers->retrieve($user->stripe_customer_id);
            } else {
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            }

            if ($isRecurring) {
                $stripeInterval = $interval === 'monthly' ? 'month' : 'year';

                // Create a product for recurring support
                $product = $stripe->products->create([
                    'name' => 'Recurring Support - ' . ucfirst($interval),
                ]);

                // Create a price for the product
                $price = $stripe->prices->create([
                    'product' => $product->id,
                    'unit_amount' => round($amount * 100),
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => $stripeInterval,
                    ],
                ]);

                // Create a SetupIntent to collect payment method
                $setupIntent = $stripe->setupIntents->create([
                    'customer' => $customer->id,
                    'payment_method_types' => ['card'],
                    'usage' => 'off_session',
                    'metadata' => [
                        'user_id' => $user->id,
                        'type' => 'recurring_support',
                        'amount' => $amount,
                        'interval' => $interval,
                        'price_id' => $price->id,
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'clientSecret' => $setupIntent->client_secret,
                    'customerId' => $customer->id,
                    'priceId' => $price->id,
                    'setupIntentId' => $setupIntent->id,
                    'type' => 'recurring',
                ]);

            } else {
                // For one-time payments, use PaymentIntent
                $paymentIntent = $stripe->paymentIntents->create([
                    'amount' => round($amount * 100),
                    'currency' => 'usd',
                    'customer' => $customer->id,
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                    'metadata' => [
                        'user_id' => $user->id,
                        'type' => 'one_time_support',
                        'amount' => $amount,
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'clientSecret' => $paymentIntent->client_secret,
                    'customerId' => $customer->id,
                    'type' => 'one_time',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating support payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while creating the support payment.',
            ], 500);
        }
    }

    /**
     * Confirm payment and update user subscription status.
     */
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

                return response()->json([
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

                return response()->json([
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
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // public function confirmRecurringSupport(Request $request)
    // {
    //     $request->validate([
    //         'setup_intent_id' => 'required|string',
    //         'price_id' => 'required|string',
    //     ]);

    //     $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    //     $user = $request->user();

    //     try {
    //         // Retrieve the setup intent
    //         $setupIntent = $stripe->setupIntents->retrieve($request->setup_intent_id);

    //         if ($setupIntent->status !== 'succeeded') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Setup intent was not successful.',
    //             ], 400);
    //         }

    //         $paymentMethodId = $setupIntent->payment_method;
    //         $amount = $setupIntent->metadata->amount ?? 0;
    //         $interval = $setupIntent->metadata->interval ?? 'monthly';

    //         // Attach payment method to customer and set as default
    //         $stripe->paymentMethods->attach($paymentMethodId, [
    //             'customer' => $user->stripe_customer_id,
    //         ]);

    //         $stripe->customers->update($user->stripe_customer_id, [
    //             'invoice_settings' => [
    //                 'default_payment_method' => $paymentMethodId,
    //             ],
    //         ]);

    //         // Create the subscription
    //         $subscription = $stripe->subscriptions->create([
    //             'customer' => $user->stripe_customer_id,
    //             'items' => [
    //                 ['price' => $request->price_id],
    //             ],
    //             'default_payment_method' => $paymentMethodId,
    //             'metadata' => [
    //                 'user_id' => $user->id,
    //                 'type' => 'recurring_support',
    //                 'amount' => $amount,
    //                 'interval' => $interval,
    //             ],
    //         ]);


    //         // Create support payment record
    //         SupportPayment::create([
    //             'user_id' => $user->id,
    //             'amount' => $amount,
    //             'type' => 'recurring',
    //             'interval' => $interval,
    //             'payment_intent_id' => $setupIntent->id,
    //             'subscription_id' => $subscription->id,
    //             'stripe_customer_id' => $user->stripe_customer_id,
    //             'status' => 'completed',
    //             'paid_at' => now(),
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'subscriptionId' => $subscription->id,
    //             'message' => 'Recurring support subscription created successfully.',
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Error confirming recurring support: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create subscription.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    /**
     * Check user payment status.
     */

    public function confirmRecurringSupport(Request $request)
{
    $request->validate([
        'setup_intent_id' => 'required|string',
        'price_id' => 'required|string',
    ]);

    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    $user = $request->user();

    try {
        // 1. Retrieve the SetupIntent to get metadata and payment method
        $setupIntent = $stripe->setupIntents->retrieve($request->setup_intent_id);

        if ($setupIntent->status !== 'succeeded') {
            return response()->json(['success' => false, 'message' => 'Setup intent incomplete.'], 400);
        }

        $paymentMethodId = $setupIntent->payment_method;
        
        // Retrieve metadata we saved during the 'createSupport' step
        $amount = $setupIntent->metadata->amount ?? 0;
        $interval = $setupIntent->metadata->interval ?? 'monthly';

        // 2. Attach Payment Method to Customer
        $stripe->paymentMethods->attach($paymentMethodId, [
            'customer' => $user->stripe_customer_id,
        ]);

        // 3. Set as Default Payment Method
        $stripe->customers->update($user->stripe_customer_id, [
            'invoice_settings' => ['default_payment_method' => $paymentMethodId],
        ]);

        // 4. Create the Subscription on Stripe
        $subscription = $stripe->subscriptions->create([
            'customer' => $user->stripe_customer_id,
            'items' => [['price' => $request->price_id]],
            'default_payment_method' => $paymentMethodId,
            'metadata' => [
                'user_id' => $user->id,
                'type' => 'recurring_support',
                'amount' => $amount,
                'interval' => $interval,
            ],
            'expand' => ['latest_invoice.payment_intent'], 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription started. Processing payment...',
            'subscription_status' => $subscription->status,
        ]);

    } catch (\Exception $e) {
        Log::error('Error confirming recurring support: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}


    public function checkPaymentStatus(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'has_paid' => $user->hasPaid(),
                'payment_status' => $user->payment_status,
                'payment_date' => $user->payment_date,
                'payment_expires_at' => $user->payment_expires_at,
                'is_expired' => $user->payment_expires_at ? $user->payment_expires_at->isPast() : false,
            ],
        ]);
    }

    /**
     * Cancel recurring support subscription.
     */
    public function cancelRecurringSupport(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|string',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $subscription = Subscription::retrieve($request->subscription_id);

            // Verify this subscription belongs to the user
            $user = $request->user();
            if ($subscription->customer !== $user->stripe_customer_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized',
                ], 403);
            }

            // Cancel the subscription
            $subscription->cancel();

            // update support payment record
            SupportPayment::where('subscription_id', $subscription->id)->update([
                'status' => 'cancelled',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recurring support has been cancelled.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling recurring support: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while cancelling the subscription.',
            ], 500);
        }
    }
}
