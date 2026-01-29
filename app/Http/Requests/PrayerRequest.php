<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrayerRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',
            'memory_verse_id' => 'nullable|exists:memory_verses,id',
            'is_answered' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title of the prayer note is required.',
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'note.string' => 'The note must be a valid string.',
            'memory_verse_id.exists' => 'The selected memory verse does not exist.',
            'is_answered.boolean' => 'The is_answered field must be true or false.',
        ];
    }
}
