<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreDevotionalRequest;
use App\Http\Requests\Admin\UpdateDevotionalRequest;
use App\Models\Devotional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DevotionalController extends BaseAdminController
{
    /**
     * Display a listing of devotionals.
     */
    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $query = Devotional::query()->with(['creator', 'publisher'])->latest('date');

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Writers only see their own devotionals
        if ($admin->can('view own') && ! $admin->can('view')) {
            $query->where('created_by', $admin->id);
        }

        $devotionals = $query->paginate(15);

        return view('admin.devotionals.index', compact('devotionals'));
    }

    /**
     * Show the form for creating a new devotional.
     */
    public function create()
    {
        return view('admin.devotionals.create');
    }

    /**
     * Store a newly created devotional.
     */
    public function store(StoreDevotionalRequest $request)
    {
        try {
            $admin = auth('admin')->user();

            $data = $request->validated();

            
            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('devotionals', 'public');
            }

            //
            $devotional = Devotional::create([
                ...$data,
                'created_by' => $admin->id,
                'status' => $request->status ?? 'draft',
            ]);


            return redirect()
                ->route('admin.devotionals.show', $devotional)
                ->with('success', 'Devotional created successfully.');
        } catch (\Exception $e) {
            Log::error('error'.$e->getMessage());

            return redirect()->back()->with('error', 'An error occure');
        }
    }

    /**
     * Display the specified devotional.
     */
    public function show(Devotional $devotional)
    {

        return view('admin.devotionals.show', compact('devotional'));
    }

    /**
     * Show the form for editing the specified devotional.
     */
    public function edit(Devotional $devotional)
    {
        return view('admin.devotionals.edit', compact('devotional'));
    }

    /**
     * Update the specified devotional.
     */
    public function update(UpdateDevotionalRequest $request, Devotional $devotional)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($devotional->image && \Storage::disk('public')->exists($devotional->image)) {
                \Storage::disk('public')->delete($devotional->image);
            }
            $data['image'] = $request->file('image')->store('devotionals', 'public');
        }

        $devotional->update($data);

        return redirect()
            ->route('admin.devotionals.show', $devotional)
            ->with('success', 'Devotional updated successfully.');
    }

    /**
     * Publish the specified devotional (Publisher only).
     */
    public function publish(Devotional $devotional)
    {

        $devotional->update([
            'status' => 'published',
            'published_at' => now(),
            'published_by' => auth('admin')->id(),
        ]);

        return redirect()
            ->route('admin.devotionals.show', $devotional)
            ->with('success', 'Devotional published successfully.');
    }

    /**
     * Unpublish the specified devotional (Publisher only).
     */
    public function unpublish(Devotional $devotional)
    {

        $devotional->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        return redirect()
            ->route('admin.devotionals.show', $devotional)
            ->with('success', 'Devotional unpublished successfully.');
    }

    /**
     * Submit devotional for review (Writer -> Editor/Publisher).
     */
    public function submitForReview(Devotional $devotional)
    {

        $devotional->update(['status' => 'in_review']);

        return redirect()
            ->route('admin.devotionals.show', $devotional)
            ->with('success', 'Devotional submitted for review.');
    }

    /**
     * Remove the specified devotional (Publisher only).
     */
    public function destroy(Devotional $devotional)
    {
        // Delete image if exists
        if ($devotional->image && \Storage::disk('public')->exists($devotional->image)) {
            \Storage::disk('public')->delete($devotional->image);
        }

        $devotional->delete();

        return redirect()
            ->route('admin.devotionals.index')
            ->with('success', 'Devotional deleted successfully.');
    }
}
