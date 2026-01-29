<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DevotionResource;
use App\Models\Devotional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DevotionalController extends Controller
{
    public function index()
    {
        try {
            $devotionals = Devotional::where('status', 'published')
                ->orderBy('date', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => DevotionResource::collection($devotionals),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching devotionals: '.$e->getMessage());

            return response()->json(['error' => 'An error occurred while fetching devotionals.'], 500);
        }
    }

    public function getDetails(Request $request)
    {
        try {
            // Check if user has paid
            $user = $request->user();

            if (! $user || ! $user->hasPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment required to access full devotional details.',
                    'requires_payment' => true,
                ], 403);
            }

            $devotional = Devotional::where('id', $request->id)
                ->where('status', 'published')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new DevotionResource($devotional),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching devotional details: '.$e->getMessage());

            return response()->json(['error' => 'An error occurred while fetching devotional details.'], 500);
        }
    }
}
