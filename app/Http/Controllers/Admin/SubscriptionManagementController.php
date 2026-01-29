<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubscriptionRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;

class SubscriptionManagementController extends Controller
{
    public function index()
    {
        $subscriptions = SubscriptionPlan::all();

        return view('admin.subscription.index', compact('subscriptions'));
    }

    public function create()
    {
        return view('admin.subscription.create');
    }

    public function store(SubscriptionRequest $request)
    {
        $request->validated();
        try {
            SubscriptionPlan::create([
                'name' => $request->name,
                'price' => $request->price,
                'interval' => $request->interval,
                'plan_id' => $request->plan_id,
                'features' => $request->features,
            ]);

            return redirect()->route('admin.subscription.index')->with('success', 'Subscription Plan Created Successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'An error occurred while creating the subscription plan.');
        }
    }

    public function edit($id)
    {
        $subscription = SubscriptionPlan::findOrFail($id);

        return view('admin.subscription.edit', compact('subscription'));
    }

    public function update(SubscriptionRequest $request, $id)
    {
        $request->validated();
        try {
            $subscription = SubscriptionPlan::findOrFail($id);
            $subscription->update([
                'name' => $request->name,
                'price' => $request->price,
                'interval' => $request->interval,
                'plan_id' => $request->plan_id,
                'features' => $request->features,
            ]);

            return redirect()->route('admin.subscription.index')->with('success', 'Subscription Plan Updated Successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'An error occurred while updating the subscription plan.');
        }
    }

    public function destroy($id)
    {
        try {
            $subscription = SubscriptionPlan::findOrFail($id);
            $subscription->delete();

            return redirect()->route('admin.subscription.index')->with('success', 'Subscription Plan Deleted Successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'An error occurred while deleting the subscription plan.');
        }
    }
}
