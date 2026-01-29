<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMemoryVerseRequest;
use App\Http\Requests\Admin\UpdateMemoryVerseRequest;
use App\Models\MemoryVerse;
use Illuminate\Support\Facades\Log;

class MemoryVerseController extends Controller
{
    public function index()
    {
        $memoryVerses = MemoryVerse::latest()->paginate(10);

        return view('admin.memory_verses.index', compact('memoryVerses'));
    }

    public function create()
    {
        return view('admin.memory_verses.create');
    }

    public function store(StoreMemoryVerseRequest $request)
    {
        try {
            MemoryVerse::create($request->validated());

            return redirect()->route('admin.memory_verses.index')->with('success', 'Memory verse created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating memory verse: '.$e->getMessage());

            return redirect()->back()->error('error', 'Failed to create memory verse: '.$e->getMessage());
        }
    }

    public function show(MemoryVerse $memoryVerse)
    {
        return view('admin.memory_verses.show', compact('memoryVerse'));
    }

    public function edit(MemoryVerse $memoryVerse)
    {
        return view('admin.memory_verses.edit', compact('memoryVerse'));
    }

    public function update(UpdateMemoryVerseRequest $request, MemoryVerse $memoryVerse)
    {
        $memoryVerse->update($request->validated());

        return redirect()->route('admin.memory_verses.index')->with('success', 'Memory verse updated successfully.');
    }

    public function destroy(MemoryVerse $memoryVerse)
    {
        $memoryVerse->delete();

        return redirect()->route('admin.memory_verses.index')->with('success', 'Memory verse deleted successfully.');
    }
}
