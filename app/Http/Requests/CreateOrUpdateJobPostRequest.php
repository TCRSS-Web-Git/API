<?php

namespace App\Http\Requests;

use App\Enums\CategoryType;
use App\Enums\JobType;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CreateOrUpdateJobPostRequest extends FormRequest
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
            'type' => ['nullable', new Enum(JobType::class), 'max:255'],
            'location_id' => ['required', Rule::exists('categories', 'id')->where('type', CategoryType::LOCATION)],
            'department_id' => ['required', Rule::exists('categories', 'id')->where('type', CategoryType::CAREER)],
            'published_at' => ['nullable', 'date'],
            // Translations
            'th' => ['array', 'required'],
            'en' => ['array'],
            'th.title' => ['required', 'string', 'max:255'],
            'en.title' => ['nullable', 'string', 'max:255'],
            'th.body' => ['required', 'string', 'max:16777215'], // max medium text
            'en.body' => ['nullable', 'string', 'max:16777215'],
            'th.meta_title' => ['nullable', 'string', 'max:100'],
            'en.meta_title' => ['nullable', 'string', 'max:100'],
            'th.meta_description' => ['nullable', 'string', 'max:160'],
            'en.meta_description' => ['nullable', 'string', 'max:160'],
            // Media (temporary media)
            'body_images' => ['array'],
            'body_images.*.id' => ['nullable'],
            'body_images.*.path' => ['required_if:id,null', 'string'],
            'body_images.*.url' => ['required', 'string'],
            'body_images.*.name' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'location_id' => Category::decodeHash($this->location_id),
            'department_id' => Category::decodeHash($this->department_id),
        ]);
    }
}
