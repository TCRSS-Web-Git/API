<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrUpdateBlogRequest extends FormRequest
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
        $requiredIfPublished = Rule::requiredIf($this->input('published_at') && Carbon::parse($this->input('published_at'))->timezone('UTC') <= now());

        return [
            'slug' => ['nullable', 'string', 'max:255', $requiredIfPublished],
            'published_at' => ['nullable', 'date', $requiredIfPublished],
            'tags' => ['array'],
            'tags.*' => ['string', 'max:255'],
            // Translations
            'th' => ['array', 'nullable', $requiredIfPublished],
            'en' => ['array', $requiredIfPublished],
            'th.title' => ['required', 'string', 'max:100'],
            'en.title' => ['nullable', 'string', 'max:100', $requiredIfPublished],
            'th.body' => ['nullable', 'string', 'max:16777215', $requiredIfPublished], // max medium text
            'en.body' => ['nullable', 'string', 'max:16777215', $requiredIfPublished],
            'th.meta_title' => ['nullable', 'string', 'max:100', $requiredIfPublished],
            'en.meta_title' => ['nullable', 'string', 'max:100', $requiredIfPublished],
            'th.meta_description' => ['nullable', 'string', 'max:160', $requiredIfPublished],
            'en.meta_description' => ['nullable', 'string', 'max:160', $requiredIfPublished],
            // Media (temporary media)
            'cover' => ['nullable', 'array', $requiredIfPublished],
            'cover.id' => ['nullable'],
            'cover.path' => ['required_if:cover.id,null', 'string'],
            'cover.url' => ['nullable', 'string'],
            'cover.name' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'array', $requiredIfPublished],
            'thumbnail.id' => ['nullable'],
            'thumbnail.path' => ['required_if:thumbnail.id,null', 'string'],
            'thumbnail.url' => ['nullable', 'string'],
            'thumbnail.name' => ['nullable', 'string'],
            'body_images' => ['array'],
            'body_images.*.id' => ['nullable'],
            'body_images.*.path' => ['required_if:id,null', 'string'],
            'body_images.*.url' => ['required', 'string'],
            'body_images.*.name' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'slug' => __('validation.attributes.url_slug'),

            'th' => __('validation.attributes.th'),
            'en' => __('validation.attributes.en'),

            'th.title' => __('validation.attributes.title_with_locale', ['locale' => 'TH']),
            'th.body' => __('validation.attributes.body_with_locale', ['locale' => 'TH']),
            'th.meta_title' => __('validation.attributes.meta_title_with_locale', ['locale' => 'TH']),
            'th.meta_description' => __('validation.attributes.meta_description_with_locale', ['locale' => 'TH']),

            'en.title' => __('validation.attributes.title_with_locale', ['locale' => 'EN']),
            'en.body' => __('validation.attributes.body_with_locale', ['locale' => 'EN']),
            'en.meta_title' => __('validation.attributes.meta_title_with_locale', ['locale' => 'EN']),
            'en.meta_description' => __('validation.attributes.meta_description_with_locale', ['locale' => 'EN']),
        ];
    }
}
