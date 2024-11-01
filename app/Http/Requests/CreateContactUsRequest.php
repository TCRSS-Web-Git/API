<?php

namespace App\Http\Requests;

use App\Enums\DepartmentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Propaganistas\LaravelPhone\Rules\Phone;

class CreateContactUsRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'phone' => ['required', (new Phone)->international()->country('TH'), 'max:20'],
            'email' => ['required', 'string', 'max:255', 'email'],
            'department_type' => ['required', new Enum(DepartmentType::class)],
            'detail' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.first_name'),
            'last_name' => __('validation.attributes.last_name'),
            'phone' => __('validation.attributes.phone'),
            'email' => __('validation.attributes.email'),
            'detail' => __('validation.attributes.detail'),
            'department_type' => __('validation.attributes.department'),
        ];
    }
}
