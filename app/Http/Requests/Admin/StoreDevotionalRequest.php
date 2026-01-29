<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDevotionalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'author' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp'],
            'key_verse' => ['required', 'string'],
            'application_note' => ['nullable', 'string'],
            'prayer_note' => ['nullable', 'string'],
            'verses' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,pending,published,in_review'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The devotional title is required.',
            'content.required' => 'The devotional content is required.',
            'author.required' => 'The author name is required.',
            'key_verse.required' => 'A key verse is required.',
            'date.required' => 'The devotional date is required.',
            'application_note.required' => 'The application note is required.',
            'prayer_note.required' => 'The prayer note is required.',
        ];
    }
}
