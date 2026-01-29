<div class="form-group mb-4">
    <label for="verse_text">Verse Text</label>
    <input type="text" name="verse_text" id="verse_text" class="form-control" value="{{ old('verse_text', $memoryVerse->verse_text ?? '') }}">
</div>
<div class="form-group mb-4">
    <label for="date">Date</label>
    <input type="date" name="date" id="date" class="form-control" value="{{ old('date', isset($memoryVerse) ? optional($memoryVerse->date)->format('Y-m-d') : '') }}">
</div>
<div class="form-group mb-4">
    <label for="notes">Notes</label>
    <textarea name="notes" id="notes" class="form-control ckeditor" rows="3">{{ old('notes', $memoryVerse->notes ?? '') }}</textarea>
</div>

<button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Create' }}</button>
