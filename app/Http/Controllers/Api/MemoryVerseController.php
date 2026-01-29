<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemoryVerseResource;
use App\Models\MemoryVerse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MemoryVerseController extends Controller
{
    public function list()
    {

        try {
            //  $user = request()->user();
            // if(!$user){
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Unauthorized'
            //     ], 401);
            // }

            $memoryVerses = MemoryVerse::orderBy('id', 'asc')->get();

            return response()->json([
                'status' => 'success',
                'data' => $memoryVerses,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching memory verses: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching memory verses.',
            ], 500);
        }
    }

    public function details($id)
    {
        try {
            // $user = request()->user();
            // if(!$user){
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Unauthorized'
            //     ], 401);
            // }

            $memoryVerse = MemoryVerse::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => new MemoryVerseResource($memoryVerse),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching memory verse details: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching memory verse details.',
            ], 500);
        }
    }
}
