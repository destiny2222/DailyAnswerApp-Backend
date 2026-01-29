<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
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
            'name' => 'required|string',
            'price' => 'required|numeric',
            'interval' => 'required|string',
            'plan_id' => 'required|string',
            'features' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'price.required' => 'Price is required',
            'interval.required' => 'Interval is required',
            'plan_id.required' => 'Plan ID is required',
            'features.array' => 'Features must be an array',
        ];
    }
}
