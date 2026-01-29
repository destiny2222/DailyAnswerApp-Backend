<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notes = Note::where('user_id', $user->id)->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => NoteResource::collection($notes),
        ]);
    }

    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $note = Note::where('id', $id)->where('user_id', $user->id)->first();

            if (! $note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new NoteResource($note),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching note: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the note.',
            ], 500);
        }
    }

    public function update(Request $request, $id){
        try {
            $request->validate([
                'content' => 'required|string',
            ]);

            $user = $request->user();
            $note = Note::where('id', $id)->where('user_id', $user->id)->firstOrFail();

            $note->title = $request->title ?? $note->title;
            $note->content = $request->content;
            $note->save();

            return response()->json([
                'success' => true,
                'message' => 'Note updated successfully.',
                'data' => new NoteResource($note),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating note: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the note.',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string',
            ]);

            $user = $request->user();

            Note::create([
                'user_id' => $user->id,
                'title' => $request->title ?? 'Untitled',
                'content' => $request->content,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Note saved successfully.',
                // 'data' => new NoteResource($note),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error saving note: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the note.',
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $note = Note::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            $note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting note: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the note.',
            ], 500);
        }
    }
}
