<?php

namespace App\Http\Requests;

use App\Enums\EducationStatus;
use App\Enums\FamilyStatus;
use App\Enums\MilitaryStatus;
use App\Enums\UserTitle;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Propaganistas\LaravelPhone\Rules\Phone;

class CreateJobApplicationRequest extends FormRequest
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
            'title' => ['required', new Enum(UserTitle::class)],
            'salary' => ['required', 'min:1'],
            'nick_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', (new Phone)->international()->country('TH'), 'max:20'],
            'date_of_birth' => ['required', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'province' => '',
            'district' => '',
            'sub_district' => '',
            'postal_code' => ['required', 'string', 'max:5'],
            'registered_province' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'email'],
            'major' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'gpa' => ['required', 'float', 'min:0'],
            'resume_file' => ['nullable', 'array'],
            'resume_file.id' => ['nullable'],
            'resume_file.path' => ['required_if:resume_file.id,null', 'string'],
            'resume_file.url' => ['nullable', 'string'],
            'resume_file.name' => ['nullable', 'string'],
            'transcript_file' => ['nullable'],
            'transcript_file.id' => ['nullable'],
            'transcript_file.path' => ['required_if:transcript_file.id,null', 'string'],
            'transcript_file.url' => ['nullable', 'string'],
            'transcript_file.name' => ['nullable', 'string'],
            'certificate_files' => ['nullable'],
            'certificate_files.id' => ['nullable'],
            'certificate_files.path' => ['required_if:certificate_files.id,null', 'string'],
            'certificate_files.url' => ['nullable', 'string'],
            'certificate_files.name' => ['nullable', 'string'],
            'photo' => ['nullable'],
            'photo.id' => ['nullable'],
            'photo.path' => ['required_if:photo.id,null', 'string'],
            'photo.url' => ['nullable', 'string'],
            'photo.name' => ['nullable', 'string'],
            'family_status' => ['required', new Enum(FamilyStatus::class)],
            'military_service' => ['required', new Enum(MilitaryStatus::class)],
            'education' => ['required', new Enum(EducationStatus::class)],
        ];
    }
}
