<?php

namespace App\Http\Requests;

use App\Enums\EducationStatus;
use App\Enums\FamilyStatus;
use App\Enums\MilitaryStatus;
use App\Enums\UserTitle;
use App\Models\Career;
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
            'career_id' => ['required', 'exists:careers,id'],
            'salary' => ['required', 'numeric', 'min:1'],
            'title' => ['required', new Enum(UserTitle::class)],
            'first_name_th' => ['required', 'string', 'max:255'],
            'last_name_th' => ['required', 'string', 'max:255'],
            'nick_name' => ['required', 'string', 'max:255'],
            'first_name_en' => ['required', 'string', 'max:255'],
            'last_name_en' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'province' => '', // TODO
            'district' => '', // TODO
            'sub_district' => '', // TODO
            'postal_code' => ['required', 'string', 'max:5'],
            'registered_province' => ['required', 'string', 'max:255'], // TODO dropdown เหมือนจังหวัด
            'phone' => ['required', (new Phone)->international()->country('TH'), 'max:20'],
            'email' => ['required', 'string', 'max:255', 'email'],
            'family_status' => ['required', new Enum(FamilyStatus::class)],
            'military_service' => ['required', new Enum(MilitaryStatus::class)],
            'education' => ['required', new Enum(EducationStatus::class)],
            'major' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'gpa' => ['required', 'decimal:2', 'min:0'],
            'resume_file' => ['nullable', 'array'],
            'resume_file.path' => ['required_if:resume_file.id,null', 'string'],
            'resume_file.url' => ['nullable', 'string'],
            'resume_file.name' => ['nullable', 'string'],
            'transcript_file' => ['nullable'],
            'transcript_file.path' => ['required_if:transcript_file.id,null', 'string'],
            'transcript_file.url' => ['nullable', 'string'],
            'transcript_file.name' => ['nullable', 'string'],
            // TODO certificate_files มีได้สูงสุด 5 ไฟล์
            'certificate_files' => ['nullable'],
            'certificate_files.path' => ['required_if:certificate_files.id,null', 'string'],
            'certificate_files.url' => ['nullable', 'string'],
            'certificate_files.name' => ['nullable', 'string'],
            'photo' => ['nullable'],
            'photo.path' => ['required_if:photo.id,null', 'string'],
            'photo.url' => ['nullable', 'string'],
            'photo.name' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'career_id' => Career::decodeHash($this->career_id),
        ]);
    }

    // TODO: แปลภาษา attributes
}
