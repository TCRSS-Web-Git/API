<?php

namespace App\Http\Requests;

use App\Enums\CategoryType;
use App\Models\Category;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:255', Rule::requiredIf($this->input('published_at') <= now())],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('type', CategoryType::BLOG), Rule::requiredIf($this->input('published_at') <= now())],
            'published_at' => ['nullable', 'date', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            'tags' => ['array'],
            'tags.*' => ['string', 'max:255'],
            // Translations
            'th' => ['array', 'nullable', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            'en' => ['array', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            'th.title' => ['required', 'string', 'max:255'],
            'en.title' => ['nullable', 'string', 'max:255', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            'th.body' => ['nullable', 'string', 'max:16777215', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())], // max medium text
            'en.body' => ['nullable', 'string', 'max:16777215', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            'th.meta_title' => ['nullable', 'string', 'max:100', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            'en.meta_title' => ['nullable', 'string', 'max:100', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            'th.meta_description' => ['nullable', 'string', 'max:160', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            'en.meta_description' => ['nullable', 'string', 'max:160', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            // Media (temporary media)
            'cover' => ['nullable', 'array', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
            'cover.id' => ['nullable'],
            'cover.path' => ['required_if:cover.id,null', 'string'],
            'cover.url' => ['nullable', 'string'],
            'cover.name' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'array', Rule::requiredIf($this->input('published_at') && $this->input('published_at') <= now())],
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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'category_id' => Category::decodeHash($this->category_id),
        ]);
    }
}
