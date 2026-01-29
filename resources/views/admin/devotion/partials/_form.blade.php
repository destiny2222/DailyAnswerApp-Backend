<div class="form-group mb-4">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $devotional->title ?? '') }}" required>
    @error('title')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group mb-4">
    <label for="date">Date</label>
    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', isset($devotional) ? $devotional->date->format('Y-m-d') : '') }}" required>
    @error('date')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group mb-4">
    <label for="text">Text</label>
    <textarea name="text" id="text" class="form-control @error('text') is-invalid @enderror" rows="3" required>{{ old('text', $devotional->text ?? '') }}</textarea>
    @error('text')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group mb-4">
    <label for="content">Content</label>
    <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="10" required>{{ old('content', $devotional->content ?? '') }}</textarea>
    @error('content')
        <span class="invalid-feedback">{{ $message }}</span>
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
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<button type="submit" class="btn btn-primary">Save</button>
