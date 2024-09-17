<?php

namespace App\Http\Requests\UserProfile;

use App\Enums\UserTitle;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserProfileRequest extends FormRequest
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
            'title' => ['nullable', new Enum(UserTitle::class)],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('role_id')) {
            $this->merge([
                'role_id' => Role::decodeHash($this->role_id),
            ]);
        }
    }

    // change attribute title localization
    public function attributes(): array
    {
        return [
            'title' => __('validation.attributes.user_title'),
        ];
    }
}
