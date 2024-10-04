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
        if ($this->input('published_at') <= now()) {
            return [
                'slug' => ['required', 'string', 'max:255'],
                'category_id' => ['required', Rule::exists('categories', 'id')->where('type', CategoryType::BLOG)],
                'published_at' => ['required', 'date'],
                'tags' => ['array'],
                'tags.*' => ['string', 'max:255'],
                // Translations
                'th' => ['array', 'required'],
                'en' => ['array', 'required'],
                'th.title' => ['required', 'string', 'max:255'],
                'en.title' => ['required', 'string', 'max:255'],
                'th.body' => ['required', 'string', 'max:16777215'], // max medium text
                'en.body' => ['required', 'string', 'max:16777215'],
                'th.meta_title' => ['required', 'string', 'max:100'],
                'en.meta_title' => ['required', 'string', 'max:100'],
                'th.meta_description' => ['required', 'string', 'max:160'],
                'en.meta_description' => ['required', 'string', 'max:160'],
                // Media (temporary media)
                'cover' => ['required', 'array'],
                'cover.id' => ['nullable'],
                'cover.path' => ['required_if:cover.id,null', 'string'],
                'cover.url' => ['nullable', 'string'],
                'cover.name' => ['nullable', 'string'],
                'thumbnail' => ['required', 'array'],
                'thumbnail.id' => ['nullable'],
                'thumbnail.path' => ['required_if:thumbnail.id,null', 'string'],
                'thumbnail.url' => ['nullable', 'string'],
                'thumbnail.name' => ['nullable', 'string'],
                'body_images' => ['array', 'nullable'],
                'body_images.*.id' => ['nullable'],
                'body_images.*.path' => ['required_if:id,null', 'string'],
                'body_images.*.url' => ['required', 'string'],
                'body_images.*.name' => ['nullable', 'string'],
            ];
        }

        return [
            'slug' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('type', CategoryType::BLOG)],
            'published_at' => ['nullable', 'date'],
            'tags' => ['array'],
            'tags.*' => ['string', 'max:255'],
            // Translations
            'th' => ['array', 'nullable'],
            'en' => ['array'],
            'th.title' => ['required', 'string', 'max:255'],
            'en.title' => ['nullable', 'string', 'max:255'],
            'th.body' => ['nullable', 'string', 'max:16777215'], // max medium text
            'en.body' => ['nullable', 'string', 'max:16777215'],
            'th.meta_title' => ['nullable', 'string', 'max:100'],
            'en.meta_title' => ['nullable', 'string', 'max:100'],
            'th.meta_description' => ['nullable', 'string', 'max:160'],
            'en.meta_description' => ['nullable', 'string', 'max:160'],
            // Media (temporary media)
            'cover' => ['nullable', 'array'],
            'cover.id' => ['nullable'],
            'cover.path' => ['required_if:cover.id,null', 'string'],
            'cover.url' => ['nullable', 'string'],
            'cover.name' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'array'],
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
