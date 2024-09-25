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
            'slug' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('type', CategoryType::BLOG)],
            'published_at' => ['nullable', 'date'],
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
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'category_id' => Category::decodeHash($this->category_id),
        ]);
    }
}
