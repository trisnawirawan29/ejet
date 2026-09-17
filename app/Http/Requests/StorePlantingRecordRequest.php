<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePlantingRecordRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'min:8', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'organization' => ['nullable', 'string', 'max:150'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'planted_at' => ['required', 'date', 'before_or_equal:today'],
            'plant_type' => ['required', 'string', 'max:120'],
            'tree_count' => ['required', 'integer', 'min:1', 'max:10000'],
            'latitude' => ['required', 'numeric', 'between:-9.2,-8.0'],
            'longitude' => ['required', 'numeric', 'between:114.0,116.0'],
            'location_name' => ['nullable', 'string', 'max:180'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'consent' => ['accepted'],
        ];
    }
}
