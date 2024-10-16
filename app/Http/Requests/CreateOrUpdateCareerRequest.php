<?php

namespace App\Http\Requests;

use App\Enums\CategoryType;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrUpdateCareerRequest extends FormRequest
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
        $requiredIfPublished = Rule::requiredIf($this->input('published_at') && Carbon::parse($this->input('published_at'))->timezone('UTC') <= now());

        return [
            'type_id' => ['nullable', $requiredIfPublished, Rule::exists('categories', 'id')->where('type', CategoryType::CAREER_TYPE)],
            'location_id' => ['nullable', $requiredIfPublished, Rule::exists('categories', 'id')->where('type', CategoryType::LOCATION)],
            'department_id' => ['nullable', $requiredIfPublished, Rule::exists('categories', 'id')->where('type', CategoryType::DEPARTMENT)],
            'published_at' => ['nullable', 'date'],
            // Translations
            'th' => ['array', 'nullable', $requiredIfPublished],
            'en' => ['array', $requiredIfPublished],
            'th.title' => ['required', 'string', 'max:100'],
            'en.title' => ['nullable', $requiredIfPublished, 'string', 'max:100'],
            'th.body' => ['nullable', $requiredIfPublished, 'string', 'max:16777215'], // max medium text
            'en.body' => ['nullable', $requiredIfPublished, 'string', 'max:16777215'],
            'th.meta_title' => ['nullable', $requiredIfPublished, 'string', 'max:100'],
            'en.meta_title' => ['nullable', $requiredIfPublished, 'string', 'max:100'],
            'th.meta_description' => ['nullable', $requiredIfPublished, 'string', 'max:160'],
            'en.meta_description' => ['nullable', $requiredIfPublished, 'string', 'max:160'],
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
            'type_id' => $this->type_id ? Category::decodeHash($this->type_id) : null,
            'location_id' => $this->location_id ? Category::decodeHash($this->location_id) : null,
            'department_id' => $this->department_id ? Category::decodeHash($this->department_id) : null,
        ]);
    }

    public function attributes(): array
    {
        return [
            'type_id' => __('validation.attributes.career_type'),
            'location_id' => __('validation.attributes.location'),
            'department_id' => __('validation.attributes.department'),

            'th' => __('validation.attributes.th'),
            'en' => __('validation.attributes.en'),

            'th.title' => __('validation.attributes.title'),
            'th.body' => __('validation.attributes.body'),
            'th.meta_title' => __('validation.attributes.meta_title'),
            'th.meta_description' => __('validation.attributes.meta_description'),

            'en.title' => __('validation.attributes.title'),
            'en.body' => __('validation.attributes.body'),
            'en.meta_title' => __('validation.attributes.meta_title'),
            'en.meta_description' => __('validation.attributes.meta_description'),
        ];
    }
}
