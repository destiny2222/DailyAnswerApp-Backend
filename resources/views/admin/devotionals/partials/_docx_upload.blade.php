<div class="alert alert-info">
    <h5><i class="fas fa-info-circle"></i> How to format your .docx file:</h5>
    <p class="mb-2">Each devotional should follow this format (in order):</p>
    <ol class="mb-0">
        <li><strong>Date:</strong> First line (e.g., "Sunday, March 29th, 2026")</li>
        <li><strong>Subheading:</strong> Second line - reading plan/reference (e.g., "Psalm in a Year. 1 Samuel 26-29")
        </li>
        <li><strong>Title:</strong> Third line, in bold/caps (e.g., "MAKE TIME TO BE QUIET BEFORE GOD")</li>
        <li><strong>Key Verse:</strong> Fourth line - the scripture text after the title</li>
        <li><strong>Content:</strong> Main paragraphs of the devotional</li>
        <li><strong>Application:</strong> Line starting with "Application:" followed by application text</li>
        <li><strong>Memory Verse:</strong> Line starting with "Memory Verse:" followed by verse text</li>
        <li><strong>Prayer:</strong> Line starting with "Prayer:" followed by prayer text</li>
        <li><strong>Separator:</strong> Use a page break or "---" on a new line to separate devotionals</li>
    </ol>
    <p class="mt-2"><small class="text-muted">Tip: Copy your existing Word devotionals - the parser will automatically
            detect this format!</small></p>
</div>

<form action="{{ route('admin.devotionals.docx-upload') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="docx_file" class="form-label">
            <i class="fas fa-file-word"></i> Upload Word Document <span class="text-danger">*</span>
        </label>
        <input type="file" name="docx_file" id="docx_file" class="form-control"
            accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            required>
        <small class="form-text text-muted">Accepts .doc and .docx files. Maximum file size: 5MB</small>
    </div>

    <div class="form-group">
        <label for="default_status" class="form-label">Default Status</label>
        <select name="default_status" id="default_status" class="form-control">
            <option value="draft">Draft</option>
            <option value="in_review">In Review</option>
            @can('publish devotional')
                <option value="published">Published</option>
            @endcan
        </select>
        <small class="form-text text-muted">This status will be used for devotionals that don't specify a status</small>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-upload"></i> Upload and Import
        </button>
        <a href="{{ route('admin.devotionals.index') }}" class="btn btn-secondary btn-lg">
            Cancel
        </a>
    </div>
</form>

<div class="mt-4">
    <h5>Example .docx Format:</h5>
    <div class="card bg-light">
        <div class="card-body">
            <pre class="mb-0" style="font-size: 0.85rem;">Sunday, March 29th, 2026
Psalm in a Year. 1 Samuel 26-29

MAKE TIME TO BE QUIET BEFORE GOD

Here's what I want you to do: Find a quiet, secluded place so you won't be tempted to role-play before God...

By the Grace of God, every morning when I get out of bed before going to work or going about my day, I like to have my quiet time. This is when I can spend time with God and seek His face by shutting off any noise...

One thing that I have learned as I continued this is that God isn't going to speak if there is lots of noise going on in your life...

Application: Find a time daily to have a quiet time with God and you will sense His presence in your life.

Memory Verse: No discipline seems pleasant at the time, but painful. Later on, however, it produces a harvest of righteousness and peace for those who have been trained by it. (Hebrews 12:11)

Prayer: Lord, help me to slow down and have a quiet time with You. Remove every distraction that is not allowing me to spend time with You in Jesus name.

---

Sunday, April 5th, 2026
Genesis 1-3

IN THE BEGINNING GOD

Content goes here...

Application: Your application text here.

Memory Verse: In the beginning God created... (Genesis 1:1)

Prayer: Your prayer text here.</pre>
        </div>
    </div>
</div>
