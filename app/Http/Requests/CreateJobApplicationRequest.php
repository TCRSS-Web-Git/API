<?php

namespace App\Http\Requests;

use App\Enums\EducationStatus;
use App\Enums\FamilyStatus;
use App\Enums\MilitaryStatus;
use App\Enums\UserTitle;
use App\Models\Career;
use App\Models\District;
use App\Models\Province;
use App\Models\Subdistrict;
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
            'province_id' => ['required', 'exists:provinces,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'sub_district_id' => ['required', 'exists:subdistricts,id'],
            'postal_code' => ['required', 'string', 'max:5', 'exists:subdistricts,zip'],
            'registered_province_id' => ['required', 'exists:provinces,id'],
            'phone' => ['required', (new Phone)->international()->country('TH'), 'max:20'],
            'email' => ['required', 'string', 'max:255', 'email'],
            'family_status' => ['required', new Enum(FamilyStatus::class)],
            'military_service' => ['required', new Enum(MilitaryStatus::class)],
            'education' => ['required', new Enum(EducationStatus::class)],
            'major' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'gpa' => ['required', 'decimal:2', 'min:0', 'max:4'],

            'resume_file' => ['required'],
            'resume_file.path' => ['required', 'string'],
            'resume_file.extension' => ['required', 'string'],
            'resume_file.mime' => ['required', 'string'],

            'transcript_file' => ['required'],
            'transcript_file.path' => ['required', 'string'],
            'transcript_file.extension' => ['required', 'string'],
            'transcript_file.mime' => ['required', 'string'],

            'certificate_files' => ['nullable', 'array', 'max:5'],
            'certificate_files.*.path' => ['nullable', 'string'],
            'certificate_files.*.extension' => ['required', 'string'],
            'certificate_files.*.mime' => ['required', 'string'],

            'photo' => ['required'],
            'photo.path' => ['required', 'string'],
            'photo.extension' => ['required', 'string'],
            'photo.mime' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'career_id' => Career::decodeHash($this->career_id),
            'province_id' => Province::decodeHash($this->province_id),
            'district_id' => District::decodeHash($this->district_id),
            'sub_district_id' => Subdistrict::decodeHash($this->sub_district_id),
            'registered_province_id' => Province::decodeHash($this->registered_province_id),
        ]);
    }

    // TODO: แปลภาษา attributes
}
