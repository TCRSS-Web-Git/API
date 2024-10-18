<?php

namespace App\Http\Requests;

use App\Models\Award;
use App\Rules\UniqueTranslation;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrUpdateAwardRequest extends FormRequest
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
        $isCreate = $this->routeIs('awards.store');
        $award = $this->route()->parameter('award') ?? new Award;

        return [
            'published_at' => ['nullable', 'date', $requiredIfPublished],
            // Translations
            'th' => ['array', 'nullable', $requiredIfPublished],
            'en' => ['array', $requiredIfPublished],
            'th.title' => ['required', 'string', 'max:100', new UniqueTranslation($isCreate, $award, 'th', 'title')],
            'en.title' => ['nullable', 'string', 'max:100', $requiredIfPublished, new UniqueTranslation($isCreate, $award, 'en', 'title')],
            'th.description' => ['nullable', 'string', 'max:16777215', $requiredIfPublished], // max medium text
            'en.description' => ['nullable', 'string', 'max:16777215', $requiredIfPublished],
            // Media (temporary media)
            'description_images' => ['array'],
            'description_images.*.id' => ['nullable'],
            'description_images.*.path' => ['required_if:id,null', 'string'],
            'description_images.*.url' => ['required', 'string'],
            'description_images.*.name' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'th' => __('validation.attributes.th'),
            'en' => __('validation.attributes.en'),

            'th.title' => __('validation.attributes.title_with_locale', ['locale' => 'TH']),
            'th.description' => __('validation.attributes.description_with_locale', ['locale' => 'TH']),

            'en.title' => __('validation.attributes.title_with_locale', ['locale' => 'EN']),
            'en.description' => __('validation.attributes.description_with_locale', ['locale' => 'EN']),
        ];
    }
}
