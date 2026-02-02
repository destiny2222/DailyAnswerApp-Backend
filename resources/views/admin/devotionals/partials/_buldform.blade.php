<form action="{{ route('admin.devotionals.bulk-store') }}" method="POST" enctype="multipart/form-data" id="bulkDevotionalForm">
    @csrf

    <div id="devotionals-container">
        <!-- First devotional form (default) -->
        <div class="devotional-item card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Devotional #1</h5>
                <button type="button" class="btn btn-sm btn-danger remove-devotional" style="display: none;">
                    Remove
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Subheading -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subheading</label>
                        <input type="text" name="devotionals[0][subheading]" 
                               class="form-control" value="{{ old('devotionals.0.subheading') }}">
                    </div>
                    <!-- Date and Status -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date/Year</label>
                        <input type="date" name="devotionals[0][date]" 
                               class="form-control" value="{{ old('devotionals.0.date') }}">
                    </div>
                    <!-- Title -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="devotionals[0][title]" 
                               class="form-control" value="{{ old('devotionals.0.title') }}" required>
                    </div>


                    <!-- Key Verse -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Key Verse</label>
                        <input type="text" name="devotionals[0][key_verse]" 
                               class="form-control" value="{{ old('devotionals.0.key_verse') }}"
                               placeholder="e.g., John 3:16">
                    </div>

                    <!-- Content -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="devotionals[0][content]" rows="6" class="form-control content-editor">{{ old('devotionals.0.content') }}</textarea>
                    </div>

                    <!-- Application Note -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Application Note</label>
                        <textarea name="devotionals[0][application_note]" rows="3"
                                  class="form-control content-editor">{{ old('devotionals.0.application_note') }}</textarea>
                    </div>

                    <!-- Verses -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Verses</label>
                        <textarea name="devotionals[0][verses]" rows="3" class="form-control content-editor"
                                  placeholder="Enter the full verse text">{{ old('devotionals.0.verses') }}</textarea>
                    </div>

                    <!-- Prayer Note -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Prayer Note</label>
                        <textarea name="devotionals[0][prayer_note]" rows="3"
                                  class="form-control content-editor">{{ old('devotionals.0.prayer_note') }}</textarea>
                    </div>

                    <!-- Image -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="devotionals[0][image]" class="form-control"
                               accept="image/jpeg,image/jpg,image/png,image/webp">
                        <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, JPG, PNG, WEBP</small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Status</label>
                        <select name="devotionals[0][status]" class="form-control">
                            <option value="draft" {{ old('devotionals.0.status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="in_review" {{ old('devotionals.0.status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                            <option value="published" {{ old('devotionals.0.status') == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add More Button -->
    <div class="mb-4">
        <button type="button" id="add-devotional" class="btn btn-success">
             Add Another Devotional
        </button>
        <span class="text-muted ml-2">(Maximum 10)</span>
    </div>

    <!-- Submit Buttons -->
    <div class="mb-4">
        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
             Create All Devotionals
        </button>
        <a href="{{ route('admin.devotionals.index') }}" class="btn btn-secondary btn-lg">
            Cancel
        </a>
    </div>
</form>

<script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let devotionalCount = 1;
    const maxDevotionals = 10;
    const container = document.getElementById('devotionals-container');
    const addButton = document.getElementById('add-devotional');

    // Add new devotional form
    addButton.addEventListener('click', function() {
        if (devotionalCount >= maxDevotionals) {
            alert('Maximum of 10 devotionals allowed');
            return;
        }

        const newIndex = devotionalCount;
        const template = createDevotionalTemplate(newIndex);
        container.insertAdjacentHTML('beforeend', template);
        devotionalCount++;

        // Initialize CKEditor for new textareas
        const newItem = container.lastElementChild;
        initializeCKEditorsInElement(newItem);

        updateRemoveButtons();
    });

    // Remove devotional
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-devotional') || e.target.closest('.remove-devotional')) {
            const button = e.target.classList.contains('remove-devotional') ? e.target : e.target.closest('.remove-devotional');
            const item = button.closest('.devotional-item');
            
            // Destroy CKEditor instances before removing
            destroyCKEditorsInElement(item);
            
            item.remove();
            devotionalCount--;
            reindexDevotionals();
            updateRemoveButtons();
        }
    });

    function createDevotionalTemplate(index) {
        return `
            <div class="devotional-item card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Devotional #${index + 1}</h5>
                    <button type="button" class="btn btn-sm btn-danger remove-devotional">
                        Remove
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Subheading -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subheading</label>
                            <input type="text" name="devotionals[${index}][subheading]" class="form-control">
                        </div>

                        <!-- Date and Status -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="devotionals[${index}][date]" class="form-control">
                        </div>
                        
                        <!-- Title -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="devotionals[${index}][title]" class="form-control" required>
                        </div>

                        <!-- Key Verse -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Key Verse</label>
                            <input type="text" name="devotionals[${index}][key_verse]" class="form-control" placeholder="e.g., John 3:16">
                        </div>

                         <!-- Content -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea name="devotionals[${index}][content]" rows="6" class="form-control content-editor"></textarea>
                        </div>

                        <!-- Application Note -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Application Note</label>
                            <textarea name="devotionals[${index}][application_note]" rows="3" class="form-control content-editor"></textarea>
                        </div>

                        <!-- Verses -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Verses</label>
                            <textarea name="devotionals[${index}][verses]" rows="3" class="form-control content-editor" placeholder="Enter the full verse text"></textarea>
                        </div>

                        <!-- Prayer Note -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Prayer Note</label>
                            <textarea name="devotionals[${index}][prayer_note]" rows="3" class="form-control content-editor"></textarea>
                        </div>

                        <!-- Image -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="devotionals[${index}][image]" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                            <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, JPG, PNG, WEBP</small>
                        </div>
                         <div class="col-md-12 mb-3">
                            <label class="form-label">Status</label>
                            <select name="devotionals[${index}][status]" class="form-control">
                                <option value="draft">Draft</option>
                                <option value="in_review">In Review</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function reindexDevotionals() {
        const items = container.querySelectorAll('.devotional-item');
        items.forEach((item, index) => {
            item.querySelector('h5').textContent = `Devotional #${index + 1}`;
            
            // Update all input names
            item.querySelectorAll('input, textarea, select').forEach(field => {
                const name = field.getAttribute('name');
                if (name) {
                    const newName = name.replace(/devotionals\[\d+\]/, `devotionals[${index}]`);
                    field.setAttribute('name', newName);
                }
            });
        });
        devotionalCount = items.length;
    }

    function updateRemoveButtons() {
        const items = container.querySelectorAll('.devotional-item');
        items.forEach((item) => {
            const removeBtn = item.querySelector('.remove-devotional');
            if (items.length > 1) {
                removeBtn.style.display = 'inline-block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    // CKEditor functions
    function initializeCKEditorsInElement(element) {
        const editors = element.querySelectorAll('.content-editor');
        editors.forEach(editor => {
            if (!editor.ckeditorInstance && !editor.classList.contains('ck-editor__editable')) {
                ClassicEditor
                    .create(editor, {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'undo', 'redo']
                    })
                    .then(newEditor => {
                        editor.ckeditorInstance = newEditor;
                        // console.log('CKEditor initialized for:', editor.name);
                    })
                    .catch(error => {
                        // console.error('CKEditor initialization error:', error);
                    });
            }
        });
    }

    function destroyCKEditorsInElement(element) {
        const editors = element.querySelectorAll('.content-editor');
        editors.forEach(editor => {
            if (editor.ckeditorInstance) {
                editor.ckeditorInstance.destroy().catch(error => console.error('CKEditor destroy error:', error));
                delete editor.ckeditorInstance;
            }
        });
    }

    // Initialize CKEditor for initial form
    setTimeout(() => {
        initializeCKEditorsInElement(document);
    }, 100);

    // Sync CKEditor data with textareas before form submit
    const form = document.getElementById('bulkDevotionalForm');
    const submitBtn = document.getElementById('submitBtn');
    let isSubmitting = false;
    
    form.addEventListener('submit', function(e) {
        // console.log('Form submit triggered');
        e.preventDefault();
        
        // Prevent double submission
        if (isSubmitting) {
            console.log('Already submitting, ignoring...');
            return false;
        }
        
        // Sync all CKEditor instances with their textareas
        let isValid = true;
        let errorMessage = '';
        
        document.querySelectorAll('.content-editor').forEach(editor => {
            if (editor.ckeditorInstance) {
                const data = editor.ckeditorInstance.getData();
                editor.value = data;
                console.log('Synced editor:', editor.name, 'Data length:', data.length);
            }
        });
        
        const items = container.querySelectorAll('.devotional-item');
        
        items.forEach((item, index) => {
            // Check if required fields are filled
            const title = item.querySelector('input[name*="[title]"]');
            const content = item.querySelector('textarea[name*="[content]"]');
            
            if (!title || !title.value.trim()) {
                isValid = false;
                errorMessage = 'Please fill in the Title for Devotional #' + (index + 1);
                return false;
            }
            
            if (!content || !content.value.trim()) {
                isValid = false;
                errorMessage = 'Please fill in the Content for Devotional #' + (index + 1);
                return false;
            }
        });
        
        if (!isValid) {
            alert(errorMessage);
            return false;
        }
        
        // console.log('Form is valid, submitting...');
        
        // Disable submit button and mark as submitting
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> Creating...';
        
        // Submit the form programmatically
        e.target.submit();
    });
});
</script>