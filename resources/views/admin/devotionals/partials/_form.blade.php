
<div class="form-group mb-4">
    <label for="subheading">Subheading</label>
    <input type="text" name="subheading" id="subheading" class="form-control @error('subheading') is-invalid @enderror" 
    value="{{ old('subheading', $devotional->subheading ?? '') }}" >
    @error('subheading')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


<div class="form-group mb-4">
    <label for="date">Date/Year</label>
    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" 
    value="{{ old('date', isset($devotional) && $devotional->date ? $devotional->date->format('Y-m-d') : '') }}">
    @error('date')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-4">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
    value="{{ old('title', $devotional->title ?? '') }}" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


<div class="form-group mb-4">
    <label for="key_verse">Key Verse</label>
    <input type="text" name="key_verse" id="key_verse" class="form-control @error('key_verse') is-invalid 
    @enderror" value="{{ old('key_verse', $devotional->key_verse ?? '') }}">
    @error('key_verse')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-4">
    <label for="content">Content</label>
    <textarea name="content" id="content" class="form-control ckeditor @error('content') is-invalid @enderror" rows="10" 
    >{{ old('content', $devotional->content ?? '') }}</textarea>
    @error('content')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-4">
    <label for="application_note">Application Note</label>
    <textarea name="application_note" id="application_note" class="form-control  @error('application_note') is-invalid @enderror" 
    rows="3">{{ old('application_note', $devotional->application_note ?? '') }}</textarea>
    @error('application_note')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-4">
    <label for="verses">Memory Verses</label>
    <textarea name="verses" id="verses" class="form-control  @error('verses') is-invalid @enderror" 
    rows="3">{{ old('verses', $devotional->verses ?? '') }}</textarea>
    @error('verses')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-4">
    <label for="prayer_note">Prayer Note</label>
    <textarea name="prayer_note" id="prayer_note" class="form-control ckeditord @error('prayer_note') is-invalid @enderror" 
    rows="3">{{ old('prayer_note', $devotional->prayer_note ?? '') }}</textarea>
    @error('prayer_note')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-4">
    <label for="image">Image</label>
    @if(isset($devotional) && $devotional->image)
        <div class="mb-2">
            <img src="{{ asset('uploads/devotionals/'.$devotional->image) }}" alt="Current image" class="img-thumbnail" style="max-width: 200px;">
            <p class="text-muted small mt-1">Current image</p>
        </div>
    @endif
    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/jpg,image/png,image/webp">
    <small class="form-text text-muted">Accepted formats: JPEG, JPG, PNG, WEBP (Max: 2MB)</small>
    @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


<div class="form-group mb-4">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
        <option value="draft" @if(old('status', $devotional->status ?? '') == 'draft') selected @endif>Draft</option>
        <option value="in_review" @if(old('status', $devotional->status ?? '') == 'in_review') selected @endif>In Review</option>
        @can('publish devotional')
            <option value="published" @if(old('status', $devotional->status ?? '') == 'published') selected @endif>Published</option>
        @endcan
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-primary w-100">Save</button>


@push('scripts')
<script>
    ClassicEditor
        .create(document.querySelector('#content'))
        .catch(error => {
            console.error(error);
        });
</script>
<script>
    ClassicEditor
        .create( document.querySelector( '#prayer_note' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
<script>
    ClassicEditor
        .create( document.querySelector( '#application_note' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
<script>
    ClassicEditor
        .create( document.querySelector( '#verses' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
@endpush