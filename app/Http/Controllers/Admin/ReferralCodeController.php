<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReferralCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferralCodeController extends BaseAdminController
{
    public function index()
    {
        $referralCodes = ReferralCode::withCount('users')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.referral_codes.index', compact('referralCodes'));
    }

    public function create()
    {
        return view('admin.referral_codes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:referral_codes'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        try {
            ReferralCode::create($validated);

            return redirect()->route('admin.referral-codes.index')->with('success', 'Referral code created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while creating the referral code.');
        }
    }

    public function show($id)
    {
        $referralCode = ReferralCode::with('users')->findOrFail($id);
        
        return view('admin.referral_codes.show', compact('referralCode'));
    }

    public function edit($id)
    {
        $referralCode = ReferralCode::findOrFail($id);

        return view('admin.referral_codes.edit', compact('referralCode'));
    }

    public function update(Request $request, $id)
    {
        $referralCode = ReferralCode::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:referral_codes,code,' . $referralCode->id],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        try {
            $referralCode->update($validated);

            return redirect()->route('admin.referral-codes.index')->with('success', 'Referral code updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while updating the referral code.');
        }
    }

    public function destroy($id)
    {
        try {
            $referralCode = ReferralCode::findOrFail($id);
            $referralCode->delete();

            return redirect()->route('admin.referral-codes.index')->with('success', 'Referral code deleted successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->route('admin.referral-codes.index')->with('error', 'An error occurred while deleting the referral code.');
        }
    }
}
