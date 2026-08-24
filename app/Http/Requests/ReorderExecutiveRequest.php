<?php

namespace App\Http\Requests;

use App\Models\Executive;
use Illuminate\Foundation\Http\FormRequest;

class ReorderExecutiveRequest extends FormRequest
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
     * The payload describes the full hierarchy: each element of `groups` is one tier
     * (its index becomes `group_order`), and the `ids` within it become `order`.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'groups' => ['present', 'array'],
            'groups.*.ids' => ['present', 'array'],
            'groups.*.ids.*' => ['required', 'exists:executives,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $groups = collect($this->input('groups', []))
            ->map(fn ($group) => [
                'ids' => collect($group['ids'] ?? [])->map(fn ($id) => Executive::decodeHash($id))->toArray(),
            ])
            ->toArray();

        $this->merge([
            'groups' => $groups,
        ]);
    }
}
