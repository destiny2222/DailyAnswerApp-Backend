<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreDevotionalRequest;
use App\Http\Requests\Admin\UpdateDevotionalRequest;
use App\Models\Devotional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpWord\IOFactory;

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

        $devotionals = $query->paginate(10);

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
            if ($devotional->image && Storage::disk('public')->exists($devotional->image)) {
                Storage::disk('public')->delete($devotional->image);
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
        if ($devotional->image && Storage::disk('public')->exists($devotional->image)) {
            Storage::disk('public')->delete($devotional->image);
        }

        $devotional->delete();

        return redirect()
            ->route('admin.devotionals.index')
            ->with('success', 'Devotional deleted successfully.');
    }

    /**
     * Show bulk create form
     */
    public function showBulkCreate()
    {
        return view('admin.devotionals.bulk-create');
    }

    /**
     * Store multiple devotionals at once
     */
    public function bulkStore(Request $request)
    {
        try {
            $admin = auth('admin')->user();
            // dd($request->all());
            // Validate the devotionals array
            $validator = Validator::make($request->all(), [
                'devotionals' => 'required|array|min:1|max:10',
                'devotionals.*.title' => 'required|string|max:255',
                'devotionals.*.subheading' => 'nullable|string|max:500',
                'devotionals.*.date' => 'nullable|date',
                'devotionals.*.key_verse' => 'nullable|string|max:1000',
                'devotionals.*.verses' => 'nullable|string',
                'devotionals.*.content' => 'required|string',
                'devotionals.*.application_note' => 'nullable|string',
                'devotionals.*.prayer_note' => 'nullable|string',
                'devotionals.*.status' => 'nullable|in:draft,in_review,published',
                'devotionals.*.image' => 'nullable|file|image|mimes:jpeg,jpg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            $createdCount = 0;

            foreach ($request->devotionals as $devotionalData) {
                // Skip empty entries (no title or content)
                if (empty($devotionalData['title']) || empty($devotionalData['content'])) {
                    continue;
                }

                // Store image if present
                $imagePath = null;
                if (isset($devotionalData['image']) && $devotionalData['image'] instanceof \Illuminate\Http\UploadedFile && $devotionalData['image']->isValid()) {
                    $imagePath = $devotionalData['image']->store('devotionals', 'public');
                }

                Devotional::create([
                    'title' => $devotionalData['title'],
                    'subheading' => $devotionalData['subheading'] ?? null,
                    'date' => $devotionalData['date'] ?? now(),
                    'key_verse' => $devotionalData['key_verse'] ?? null,
                    'verses' => $devotionalData['verses'] ?? null,
                    'content' => $devotionalData['content'],
                    'application_note' => $devotionalData['application_note'] ?? null,
                    'prayer_note' => $devotionalData['prayer_note'] ?? null,
                    'status' => $devotionalData['status'] ?? 'draft',
                    'image' => $imagePath,
                    'created_by' => $admin->id,
                ]);

                $createdCount++;
            }

            DB::commit();

            if ($createdCount === 0) {
                return redirect()
                    ->back()
                    ->with('error', 'No devotionals were created. Please fill in at least one devotional.');
            }

            return redirect()
                ->route('admin.devotionals.index')
                ->with('success', "Successfully created {$createdCount} devotional(s)");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk store error: '.$e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while processing your request.');
        }
    }

    /**
     * Upload and parse .docx file to create devotionals
     * Note: .doc files (Word 97-2003) are not supported due to limited parser compatibility
     */
    public function docxUpload(Request $request)
    {
        // Explicitly check for ZipArchive class
        if (! class_exists('ZipArchive')) {
            return redirect()->back()->with('error', 'Server configuration error: The ZipArchive class was not found. Please ensure the PHP "zip" extension is enabled in your php.ini file and that your web server (Apache) has been fully restarted.');
        }

        $request->validate([
            'docx_file' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:5120',
            'default_status' => 'nullable|in:draft,in_review,published',
        ]);

        try {
            $admin = auth('admin')->user();
            $file = $request->file('docx_file');
            $defaultStatus = $request->input('default_status', 'draft');

            // Validate the file exists and is readable
            if (! $file->isValid()) {
                return redirect()
                    ->back()
                    ->with('error', 'The uploaded file is invalid or corrupted.');
            }

            $filePath = $file->getRealPath();
            $extension = strtolower($file->getClientOriginalExtension());

            // Ensure the file exists and is readable
            if (! file_exists($filePath) || ! is_readable($filePath)) {
                return redirect()
                    ->back()
                    ->with('error', 'Unable to read the uploaded file.');
            }

            // Load the Word file with the appropriate reader based on extension
            if ($extension === 'doc') {
                // The .doc format (Word 97-2003) has limited support
                // Recommend converting to .docx for better compatibility
                return redirect()
                    ->back()
                    ->with('error', 'The old .doc format (Word 97-2003) has limited support and may not parse correctly. Please save your document as .docx (Word 2007 or later) and try again. In Microsoft Word: File → Save As → Save as type → Word Document (.docx)');
            } elseif ($extension === 'docx') {
                // Verify ZIP structure for .docx files
                $zip = new \ZipArchive;
                if ($zip->open($filePath) !== true) {
                    return redirect()
                        ->back()
                        ->with('error', 'The uploaded file is not a valid DOCX document. Please ensure you are uploading a proper Word document.');
                }
                $zip->close();

                // Use Word2007 reader for .docx files
                $phpWord = IOFactory::load($filePath, 'Word2007');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Unsupported file format. Please upload a .docx file.');
            }

            $text = '';

            // Extract text from all sections
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= $this->extractTextFromElement($element);
                }
            }

            // Parse the text into devotionals
            $devotionals = $this->parseDevotionalText($text, $defaultStatus);

            if (empty($devotionals)) {
                return redirect()
                    ->back()
                    ->with('error', 'No devotionals found in the uploaded file. Please check the format.');
            }

            // Store devotionals in database
            DB::beginTransaction();

            $createdCount = 0;
            $errors = [];

            foreach ($devotionals as $index => $devotionalData) {
                if (! empty($devotionalData['title']) && ! empty($devotionalData['content'])) {
                    try {
                        // Parse the date - handle various date formats
                        $parsedDate = null;
                        if (! empty($devotionalData['date'])) {
                            try {
                                $parsedDate = \Carbon\Carbon::parse($devotionalData['date'])->format('Y-m-d');
                            } catch (\Exception $e) {
                                // If parsing fails, use today's date
                                $parsedDate = now()->format('Y-m-d');
                                Log::warning("Could not parse date '{$devotionalData['date']}', using today's date instead");
                            }
                        } else {
                            $parsedDate = now()->format('Y-m-d');
                        }

                        Devotional::create([
                            'title' => $devotionalData['title'],
                            'subheading' => $devotionalData['subheading'] ?? null,
                            'date' => $parsedDate,
                            'key_verse' => $devotionalData['key_verse'] ?? null,
                            'verses' => $devotionalData['verses'] ?? null,
                            'content' => $devotionalData['content'],
                            'application_note' => $devotionalData['application_note'] ?? null,
                            'prayer_note' => $devotionalData['prayer_note'] ?? null,
                            'status' => $devotionalData['status'] ?? $defaultStatus,
                            'created_by' => $admin->id,
                        ]);

                        $createdCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Devotional #{$index}: ".$e->getMessage();
                        Log::error("Failed to create devotional #{$index}: ".$e->getMessage(), ['data' => $devotionalData]);
                    }
                }
            }

            DB::commit();

            if ($createdCount === 0) {
                $errorMessage = 'No devotionals were created.';
                if (! empty($errors)) {
                    $errorMessage .= ' Errors: '.implode(', ', $errors);
                }

                return redirect()
                    ->back()
                    ->with('error', $errorMessage);
            }

            $successMessage = "Successfully imported {$createdCount} devotional(s) from .docx file";
            if (! empty($errors)) {
                $successMessage .= '. Some failed: '.implode(', ', array_slice($errors, 0, 3));
            }

            return redirect()
                ->route('admin.devotionals.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DOCX upload error: '.$e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while processing the file: '.$e->getMessage());
        }
    }

    /**
     * Parse text content into devotional data
     * Expected format:
     * Line 1: Date (e.g., "Sunday, March 29th, 2026")
     * Line 2: Reference/Verses (e.g., "Psalm in a Year. 1 Samuel 26-29")
     * Line 3: Title (usually in caps/bold)
     * Line 4+: Key verse text (optional)
     * Content paragraphs
     * Application: ...
     * Memory Verse: ...
     * Prayer: ...
     * --- (separator for multiple devotionals)
     */
    private function parseDevotionalText(string $text, string $defaultStatus): array
    {
        $devotionals = [];

        // Split into lines first
        $lines = explode("\n", $text);
        $allLines = array_map('trim', $lines);

        // Find indices where new devotionals start (lines that look like dates)
        $devotionalStarts = [];
        $datePattern = '/^(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday),?\s+/i';

        foreach ($allLines as $index => $line) {
            if (preg_match($datePattern, $line) && ! empty($line)) {
                $devotionalStarts[] = $index;
            }
        }

        // If no dates found, try splitting by --- separator or form feed
        if (empty($devotionalStarts)) {
            $rawDevotionals = preg_split('/\n---+\n|\f/', $text);
            foreach ($rawDevotionals as $raw) {
                $parsed = $this->parseSingleDevotional($raw, $defaultStatus);
                if ($parsed) {
                    $devotionals[] = $parsed;
                }
            }

            return $devotionals;
        }

        // Extract each devotional section
        for ($i = 0; $i < count($devotionalStarts); $i++) {
            $startIndex = $devotionalStarts[$i];
            $endIndex = isset($devotionalStarts[$i + 1]) ? $devotionalStarts[$i + 1] : count($allLines);

            $devotionalLines = array_slice($allLines, $startIndex, $endIndex - $startIndex);
            $devotionalText = implode("\n", $devotionalLines);

            $parsed = $this->parseSingleDevotional($devotionalText, $defaultStatus);
            if ($parsed) {
                $devotionals[] = $parsed;
            }
        }

        return $devotionals;
    }

    /**
     * Parse a single devotional from text
     */
    private function parseSingleDevotional(string $text, string $defaultStatus): ?array
    {
        $text = trim($text);
        if (empty($text)) {
            return null;
        }

        $devotional = [
            'title' => '',
            'subheading' => null,
            'date' => null,
            'key_verse' => null,
            'verses' => null,
            'content' => '',
            'application_note' => null,
            'prayer_note' => null,
            'status' => $defaultStatus,
        ];

        // Split into lines
        $lines = explode("\n", $text);
        $nonEmptyLines = array_filter(array_map('trim', $lines), function ($line) {
            return ! empty($line);
        });
        $nonEmptyLines = array_values($nonEmptyLines);

        if (count($nonEmptyLines) < 3) {
            return null; // Need at least date, reference, and title
        }

        // Line 1: Date
        $devotional['date'] = $nonEmptyLines[0];

        // Line 2: Reference/Reading plan (goes to subheading)
        $devotional['subheading'] = $nonEmptyLines[1];

        // Line 3: Title (usually all caps or bold)
        $devotional['title'] = $nonEmptyLines[2];

        // Line 4: Key verse (the scripture text after the title)
        $keyVerseIndex = 3;
        if (isset($nonEmptyLines[$keyVerseIndex])) {
            $devotional['key_verse'] = $nonEmptyLines[$keyVerseIndex];
        }

        // Process remaining content (starting from line 5)
        $contentLines = [];
        $applicationLines = [];
        $memoryVerseLines = [];
        $prayerLines = [];
        $currentSection = 'content';

        for ($i = 4; $i < count($nonEmptyLines); $i++) {
            $line = $nonEmptyLines[$i];

            // Check for section markers
            if (preg_match('/^Application:\s*(.*)$/i', $line, $matches)) {
                $currentSection = 'application';
                $remainder = trim($matches[1]);
                if (! empty($remainder)) {
                    $applicationLines[] = $remainder;
                }
            } elseif (preg_match('/^Memory\s*Verse:\s*(.*)$/i', $line, $matches)) {
                $currentSection = 'memory_verse';
                $remainder = trim($matches[1]);
                if (! empty($remainder)) {
                    $memoryVerseLines[] = $remainder;
                }
            } elseif (preg_match('/^Prayer:\s*(.*)$/i', $line, $matches)) {
                $currentSection = 'prayer';
                $remainder = trim($matches[1]);
                if (! empty($remainder)) {
                    $prayerLines[] = $remainder;
                }
            } else {
                // Add to current section
                if ($currentSection === 'content') {
                    $contentLines[] = $line;
                } elseif ($currentSection === 'application') {
                    $applicationLines[] = $line;
                } elseif ($currentSection === 'memory_verse') {
                    $memoryVerseLines[] = $line;
                } elseif ($currentSection === 'prayer') {
                    $prayerLines[] = $line;
                }
            }
        }

        $devotional['content'] = implode("\n\n", $contentLines);
        $devotional['application_note'] = ! empty($applicationLines) ? implode("\n\n", $applicationLines) : null;

        // Memory verse goes to verses field
        if (! empty($memoryVerseLines)) {
            $devotional['verses'] = implode(' ', $memoryVerseLines);
        }

        $devotional['prayer_note'] = ! empty($prayerLines) ? implode("\n\n", $prayerLines) : null;

        // Only return if we have a title and content
        if (! empty($devotional['title']) && ! empty($devotional['content'])) {
            return $devotional;
        }

        return null;
    }

    /**
     * Recursively extract text from PhpWord elements
     */
    private function extractTextFromElement($element): string
    {
        $text = '';

        // Handle TextRun elements (contains Text elements)
        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $textElement) {
                if (method_exists($textElement, 'getText')) {
                    $textContent = $textElement->getText();
                    // Ensure we only add strings
                    if (is_string($textContent)) {
                        $text .= $textContent;
                    }
                }
            }
            $text .= "\n";
        }
        // Handle Table elements
        elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $cellElement) {
                        $text .= $this->extractTextFromElement($cellElement);
                    }
                }
            }
        }
        // Handle ListItem elements (bullet points, numbered lists)
        elseif ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
            $text .= $this->extractTextFromElement($element->getTextObject());
        }
        // Handle Text elements and other elements with getText method
        elseif (method_exists($element, 'getText') && ! $element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            $textContent = $element->getText();
            // Ensure we only add strings
            if (is_string($textContent)) {
                $text .= $textContent."\n";
            }
        }
        // Handle elements with nested elements
        elseif (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $childElement) {
                $text .= $this->extractTextFromElement($childElement);
            }
        }

        return $text;
    }
}
