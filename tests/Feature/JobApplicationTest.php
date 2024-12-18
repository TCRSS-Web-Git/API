<?php

namespace Tests\Feature;

use App\Enums\EducationStatus;
use App\Enums\FamilyStatus;
use App\Enums\MilitaryStatus;
use App\Enums\UserTitle;
use App\Mail\JobApplication;
use App\Models\Career;
use App\Models\District;
use App\Models\Province;
use App\Models\Subdistrict;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_job_application_form(): void
    {
        $mail1 = 'test1@gmail.com';
        $mail2 = 'test2@hotmail.com';
        Config::set('tcrss.mails_for_job_application', [$mail1, $mail2]);

        $career = Career::factory()->create();
        $province = Province::factory()->create();
        $district = District::factory()->create();
        $subDistrict = Subdistrict::factory()->create();

        $resume = $this->postJson(route('public.temporary_media.store'), ['media' => UploadedFile::fake()->image('resume.jpg')]);
        $transcript = $this->postJson(route('public.temporary_media.store'), ['media' => UploadedFile::fake()->image('transcript.pdf')]);
        $photo = $this->postJson(route('public.temporary_media.store'), ['media' => UploadedFile::fake()->image('photo.png')]);
        $certificateA = $this->postJson(route('public.temporary_media.store'), ['media' => UploadedFile::fake()->image('certificateA.pdf')]);
        $certificateB = $this->postJson(route('public.temporary_media.store'), ['media' => UploadedFile::fake()->image('certificateA.pdf')]);

        Mail::fake();

        $response = $this->postJson(route('public.job_applications.create'), [
            'career_id' => $career->hashid,
            'salary' => 123_456,
            'title' => UserTitle::MR->value,
            'first_name_th' => $firstNameTH = 'เทย์เลอร์',
            'last_name_th' => $lastNameTH = 'สวิฟต์',
            'nick_name' => 'เทย์เทย์',
            'first_name_en' => 'TAYLOR',
            'last_name_en' => 'SWIFT',
            'date_of_birth' => '1989-12-13',
            'address' => 'address',
            'province_id' => $province->hashid,
            'district_id' => $district->hashid,
            'sub_district_id' => $subDistrict->hashid,
            'postal_code' => $subDistrict->zip,
            'registered_province_id' => $province->hashid,
            'phone' => fake()->e164PhoneNumber(),
            'email' => fake()->email(),
            'family_status' => FamilyStatus::SINGLE->value,
            'military_service' => MilitaryStatus::WOMAN->value,
            'education' => EducationStatus::DOCTOR_DEGREE->value,
            'major' => 'fine arts degree',
            'institution' => 'NYU',
            'gpa' => 3.98,
            'resume_file' => [
                'path' => $resume->json('data.path'),
                'extension' => $resume->json('data.extension'),
                'mime' => $resume->json('data.mime'),
            ],
            'transcript_file' => [
                'path' => $transcript->json('data.path'),
                'extension' => $transcript->json('data.extension'),
                'mime' => $transcript->json('data.mime'),
            ],
            'photo' => [
                'path' => $photo->json('data.path'),
                'extension' => $photo->json('data.extension'),
                'mime' => $photo->json('data.mime'),
            ],
            'certificate_files' => [
                [
                    'path' => $certificateA->json('data.path'),
                    'extension' => $certificateA->json('data.extension'),
                    'mime' => $certificateA->json('data.mime'),
                ],
                [
                    'path' => $certificateB->json('data.path'),
                    'extension' => $certificateB->json('data.extension'),
                    'mime' => $certificateB->json('data.mime'),
                ],
            ],
        ]);

        $response->assertStatus(201);
        Mail::assertQueued(JobApplication::class, function (JobApplication $mail) use ($firstNameTH, $lastNameTH, $mail1) {
            return $mail->hasTo($mail1) &&
                $mail->hasSubject("Job Application from $firstNameTH $lastNameTH");
        });

        Mail::assertQueued(JobApplication::class, function (JobApplication $mail) use ($firstNameTH, $lastNameTH, $mail2) {
            return $mail->hasTo($mail2) &&
                $mail->hasSubject("Job Application from $firstNameTH $lastNameTH");
        });
    }
}
