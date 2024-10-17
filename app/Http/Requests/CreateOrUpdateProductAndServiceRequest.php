<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrUpdateProductAndServiceRequest extends FormRequest
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
        $titleUnique = $this->routeIs('products-and-services.store') ? Rule::unique('product_and_service_translations', 'title') : null;

        return [
            'published_at' => ['nullable', 'date', $requiredIfPublished],
            'th' => ['array', 'nullable', $requiredIfPublished],
            'en' => ['array', $requiredIfPublished],
            'th.title' => ['required', 'string', 'max:100', $titleUnique],
            'en.title' => ['nullable', $requiredIfPublished, 'string', 'max:100', $titleUnique],
            'cover' => ['nullable', 'array', $requiredIfPublished],
            'cover.id' => ['nullable'],
            'cover.path' => ['required_if:cover.id,null', 'string'],
            'cover.url' => ['nullable', 'string'],
            'cover.name' => ['nullable', 'string'],
            'file' => ['nullable', 'array', $requiredIfPublished],
            'file.id' => ['nullable'],
            'file.path' => ['required_if:file.id,null', 'string'],
            'file.url' => ['nullable', 'string'],
            'file.name' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'th' => __('validation.attributes.th'),
            'en' => __('validation.attributes.en'),

            'th.title' => __('validation.attributes.title'),
            'en.title' => __('validation.attributes.title'),

            'file' => __('validation.attributes.file'),
        ];
    }
}
