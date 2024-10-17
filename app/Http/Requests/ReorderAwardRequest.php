<?php

namespace App\Http\Requests;

use App\Models\Award;
use Illuminate\Foundation\Http\FormRequest;

class ReorderAwardRequest extends FormRequest
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
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:awards,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $ids = collect($this->ids)->map(fn ($id) => Award::decodeHash($id))->toArray();
        $this->merge([
            'ids' => $ids,
        ]);
    }
}
