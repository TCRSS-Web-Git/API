<?php

namespace App\Http\Requests;

use App\Enums\UserTitle;
use App\Models\Role;
use App\Traits\ValidatePhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Propaganistas\LaravelPhone\Rules\Phone;

class CreateUserRequest extends FormRequest
{
    use ValidatePhone;

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
            'role_id' => ['required', 'exists:roles,id'],
            'title' => ['nullable', new Enum(UserTitle::class)],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', (new Phone)->international()->country('TH'), 'max:20'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = $this->validateAndTransformPhone($this->all(), 'phone');
        $data['role_id'] = $this->input('role_id') ? Role::decodeHash($this->input('role_id')) : null;
        $this->merge($data);
    }

    public function attributes(): array
    {
        return [
            'title' => __('validation.attributes.user_title'),
            'role_id' => __('validation.attributes.role'),
        ];
    }
}
