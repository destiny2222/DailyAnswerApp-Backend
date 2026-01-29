<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrayerResource;
use App\Models\PrayerNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrayerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $prayerNote = PrayerNote::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => PrayerResource::collection($prayerNote),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();
            $prayerNote = PrayerNote::create([
                'user_id' => $user->id,
                'memory_verse_id' => $request->input('memory_verse_id'),
                'title' => $request->input('title'),
                'note' => $request->input('note'),
                'is_answered' => $request->input('is_answered', false),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prayer note saved successfully.',
                'data' => new PrayerResource($prayerNote),
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving prayer note: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the prayer note.',
            ], 500);
        }
    }

    public function show(PrayerNote $prayerNote)
    {

        return response()->json([
            'success' => true,
            'data' => new PrayerResource($prayerNote),
        ]);
    }

    public function update(Request $request, PrayerNote $prayerNote)
    {
        try {
            $user = $request->user();
            $prayerNote->update([
                'user_id' => $user->id,
                'memory_verse_id' => $request->input('memory_verse_id'),
                'title' => $request->input('title'),
                'note' => $request->input('note'),
                'is_answered' => $request->input('is_answered'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prayer note updated successfully.',
                'data' => new PrayerResource($prayerNote),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating prayer note: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the prayer note.',
            ], 500);
        }
    }

    public function delete(PrayerNote $prayerNote)
    {
        try {
            $prayerNote->delete();

            return response()->json([
                'success' => true,
                'message' => 'Prayer note deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting prayer note: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the prayer note.',
            ], 500);
        }
    }
}
