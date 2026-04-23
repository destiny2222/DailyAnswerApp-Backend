<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreDevotionalRequest;
use App\Http\Requests\Admin\UpdateDevotionalRequest;
use App\Models\Devotional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
                $image = $request->file('image');
                $filename = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads/devotionals'), $filename);
                $data['image'] = $filename;
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
            if ($devotional->image && File::exists(public_path($devotional->image))) {
                File::delete(public_path($devotional->image));
            }
            $image = $request->file('image');
            $filename = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads/devotionals'), $filename);
            $data['image'] = $filename;
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
        if ($devotional->image && File::exists(public_path($devotional->image))) {
            File::delete(public_path($devotional->image));
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
                    $image = $devotionalData['image'];
                    $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                    $image->move(public_path('uploads/devotionals'), $filename);
                    $imagePath = $filename;
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
        $request->validate([
            'docx_file' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword|max:5120',
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
                // Use MsDoc reader for .doc files
                try {
                    $phpWord = IOFactory::load($filePath, 'MsDoc');
                } catch (\Exception $e) {
                    return redirect()
                        ->back()
                        ->with('error', 'The old .doc format (Word 97-2003) could not be parsed. Please save your document as .docx (Word 2007 or later) and try again.');
                }
            } elseif ($extension === 'docx') {
                // Explicitly check for ZipArchive class for .docx
                if (! class_exists('ZipArchive')) {
                    return redirect()->back()->with('error', 'Server configuration error: The ZipArchive class was not found. Please ensure the PHP "zip" extension is enabled.');
                }

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
                    ->with('error', 'Unsupported file format. Please upload a .docx or .doc file.');
            }

            $text = '';

            // Extract text from all sections
            $sections = $phpWord->getSections();
            if (count($sections) > 0) {
                foreach ($sections as $section) {
                    foreach ($section->getElements() as $element) {
                        $text .= $this->extractTextFromElement($element);
                    }
                }
            }

            // Fallback for .docx: If no text was extracted via PHPWord, try raw ZIP extraction
            if (empty(trim($text)) && $extension === 'docx') {
                Log::info('PHPWord extracted no text, attempting raw ZIP extraction for DOCX.');
                $text = $this->extractRawTextFromDocx($filePath);
            }

            // Normalize text: fix non-breaking spaces and redundant horizontal spaces
            $text = str_replace(["\xc2\xa0", "\xa0"], ' ', $text);
            $text = preg_replace('/[ \t]+/', ' ', $text);

            // Parse the text into devotionals
            $devotionals = $this->parseDevotionalText($text, $defaultStatus);

            if (empty($devotionals)) {
                Log::warning('DOCX parsing returned no devotionals. Extracted text sample: '.substr($text, 0, 500));

                return redirect()
                    ->back()
                    ->with('error', 'No devotionals found in the uploaded file. Please ensure your document follows the required format (Date, Subheading, Title, etc.) and uses standard line breaks.');
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

    private function parseDevotionalText(string $text, string $defaultStatus): array
    {
        $devotionals = [];

        // Split into lines first (handle different line endings)
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $allLines = array_map('trim', $lines);

        // Find indices where new devotionals start (lines that look like dates)
        $devotionalStarts = [];

        // More flexible date patterns:
        $datePatterns = [
            // 1. Day of week prefix: "Monday, March 29..." or "Wednes day" (lenient)
            '/^(mon|tue|wed|thu|fri|sat|sun|monday|tuesday|wednesday|thursday|friday|saturday|sunday)[\s,.]+/i',
            // 2. Month name first: "March 29..." or "Mar 29..."
            '/^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|may|june|july|august|september|october|november|december)[\s,.]+\d{1,2}/i',
            // 3. Numeric or Day first: "29 March..." or "29/03/2026"
            '/^(\d{1,2}[\s,.]+(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|\w+)\w*[\s,.]+\d{4}|\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i',
            // 4. Year first: "2026-03-29"
            '/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}/',
        ];

        foreach ($allLines as $index => $line) {
            if (! empty($line)) {
                // Skip common document headers
                if (preg_match('/^(The Daily Answer|Page \d+|Edition)/i', $line)) {
                    continue;
                }

                foreach ($datePatterns as $pattern) {
                    if (preg_match($pattern, $line)) {
                        $devotionalStarts[] = $index;
                        break;
                    }
                }
            }
        }

        // If no dates found with date patterns, try splitting by --- separator or form feed
        if (empty($devotionalStarts)) {
            $rawDevotionals = preg_split('/\n---+\n|\f/', $text);
            foreach ($rawDevotionals as $raw) {
                $parsed = $this->parseSingleDevotional($raw, $defaultStatus);
                if ($parsed) {
                    $devotionals[] = $parsed;
                }
            }

            // If still no devotionals found, try harder parsing - look for title-like lines (all caps or bold markers)
            if (empty($devotionals)) {
                $devotionals = $this->parseDevotionalTextFallback($text, $defaultStatus);
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

        // Split into lines (handle different line endings)
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $nonEmptyLines = array_filter(array_map('trim', $lines), function ($line) {
            return ! empty($line);
        });
        $nonEmptyLines = array_values($nonEmptyLines);

        if (count($nonEmptyLines) < 2) {
            return null; // Need at least date and title/content
        }

        // Line 1: Date
        $devotional['date'] = $nonEmptyLines[0];

        // Line 2 & 3: Determine which is Subheading and which is Title
        // Heuristic: If Line 2 is all-caps and Line 3 is not, Line 2 is likely the Title.
        // If both are present and Line 2 is mixed case or Line 3 is all-caps, follow standard format.
        if (count($nonEmptyLines) >= 3) {
            $line2 = $nonEmptyLines[1];
            $line3 = $nonEmptyLines[2];
            
            $line2IsCaps = preg_match('/^[A-Z\s\d\W]{5,}$/', $line2) && !preg_match('/[a-z]/', $line2);
            $line3IsCaps = preg_match('/^[A-Z\s\d\W]{5,}$/', $line3) && !preg_match('/[a-z]/', $line3);

            if ($line2IsCaps && !$line3IsCaps) {
                // Case: Date, Title, Content
                $devotional['title'] = $line2;
                $devotional['subheading'] = null;
                $contentStartIndex = 2;
            } else {
                // Case: Date, Subheading, Title (or default)
                $devotional['subheading'] = $line2;
                $devotional['title'] = $line3;
                $contentStartIndex = 3;
            }
        } else {
            // Fallback for very short devotionals
            $devotional['title'] = $nonEmptyLines[1] ?? 'Untitled';
            $contentStartIndex = 2;
        }

        // Line 4 (or next): Key verse (the scripture text after the title)
        if (isset($nonEmptyLines[$contentStartIndex])) {
            $devotional['key_verse'] = $nonEmptyLines[$contentStartIndex];
            $contentStartIndex++;
        }

        // Process remaining content
        $contentLines = [];
        $applicationLines = [];
        $memoryVerseLines = [];
        $prayerLines = [];
        $currentSection = 'content';

        for ($i = $contentStartIndex; $i < count($nonEmptyLines); $i++) {
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
     * Fallback parsing when no date patterns are matched
     * Looks for titles (all caps lines) as potential separators
     */
    private function parseDevotionalTextFallback(string $text, string $defaultStatus): array
    {
        $devotionals = [];

        // Try splitting by common separators first
        $separators = ["\n---\n", "\n***\n", "\f"]; // \f is page break
        $rawSections = [];

        foreach ($separators as $sep) {
            if (str_contains($text, $sep)) {
                $rawSections = explode($sep, $text);
                break;
            }
        }

        if (empty($rawSections)) {
            // Last resort: try to find titles (all caps lines)
            $lines = preg_split('/\r\n|\r|\n/', $text);
            $currentSection = [];

            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (empty($trimmed)) {
                    continue;
                }

                // If it looks like a title (All caps, more than 5 chars, less than 100)
                // and it's not a common label like "Application:"
                if (preg_match('/^[A-Z\s\d\W]+$/', $trimmed) &&
                    strlen($trimmed) > 5 &&
                    strlen($trimmed) < 100 &&
                    ! preg_match('/^(APPLICATION|MEMORY VERSE|PRAYER):/i', $trimmed)) {

                    if (! empty($currentSection)) {
                        $parsed = $this->parseSingleDevotional(implode("\n", $currentSection), $defaultStatus);
                        if ($parsed) {
                            $devotionals[] = $parsed;
                        }
                    }
                    $currentSection = [$trimmed];
                } else {
                    $currentSection[] = $line;
                }
            }

            if (! empty($currentSection)) {
                $parsed = $this->parseSingleDevotional(implode("\n", $currentSection), $defaultStatus);
                if ($parsed) {
                    $devotionals[] = $parsed;
                }
            }
        } else {
            foreach ($rawSections as $raw) {
                $parsed = $this->parseSingleDevotional($raw, $defaultStatus);
                if ($parsed) {
                    $devotionals[] = $parsed;
                }
            }
        }

        return $devotionals;
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

    /**
     * Raw extraction from DOCX ZIP structure
     * Useful when PHPWord fails to parse the structure but the XML is present
     */
    private function extractRawTextFromDocx(string $filePath): string
    {
        $text = '';
        $zip = new \ZipArchive;
        if ($zip->open($filePath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            if ($xml) {
                // Replace paragraph end tags with newlines to preserve structure
                $xml = str_replace('</w:p>', "</w:p>\n", $xml);
                // Strip all XML tags
                $text = strip_tags($xml);
                // Decode HTML entities just in case
                $text = html_entity_decode($text);
            }
        }

        return $text;
    }

    /**
     * Bulk delete devotionals
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No items selected for deletion.');
        }

        try {
            DB::beginTransaction();
            
            // Filter IDs to ensure user has permission to delete them
            $devotionals = Devotional::whereIn('id', $ids)->get();
            $deletedCount = 0;

            foreach ($devotionals as $devotional) {
                // Check if the current admin can delete this specific devotional
                if (auth('admin')->user()->can('devotionals.delete', $devotional)) {
                    $devotional->delete();
                    $deletedCount++;
                }
            }

            DB::commit();

            if ($deletedCount > 0) {
                return redirect()->back()->with('success', "Successfully deleted {$deletedCount} devotional(s).");
            } else {
                return redirect()->back()->with('error', 'No devotionals were deleted. You might not have the required permissions.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during bulk deletion.');
        }
    }
}
