<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateExecutiveRequest extends FormRequest
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
            'th' => ['array'],
            'en' => ['array', 'nullable'],
            'th.name' => ['required', 'string', 'max:255'],
            'en.name' => ['nullable', 'string', 'max:255'],
            'th.position' => ['nullable', 'string', 'max:255'],
            'en.position' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'array'],
            'image.id' => ['nullable'],
            'image.path' => ['required_if:image.id,null', 'string'],
            'image.url' => ['nullable', 'string'],
            'image.name' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'th' => __('validation.attributes.th'),
            'en' => __('validation.attributes.en'),

            'th.name' => __('validation.attributes.name_with_locale', ['locale' => 'TH']),
            'en.name' => __('validation.attributes.name_with_locale', ['locale' => 'EN']),
            'th.position' => __('validation.attributes.position_with_locale', ['locale' => 'TH']),
            'en.position' => __('validation.attributes.position_with_locale', ['locale' => 'EN']),

            'image' => __('validation.attributes.image'),
        ];
    }
}
