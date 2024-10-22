<?php

namespace App\Mail;

use App\Enums\EducationStatus;
use App\Enums\FamilyStatus;
use App\Enums\MilitaryStatus;
use App\Enums\UserTitle;
use App\Models\Career;
use App\Models\District;
use App\Models\Province;
use App\Models\Subdistrict;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class JobApplication extends Mailable
{
    use Queueable, SerializesModels;

    protected Career $career;

    protected int $salary;

    protected UserTitle $title;

    protected string $firstNameTH;

    protected string $lastNameTH;

    protected string $firstNameEN;

    protected string $lastNameEN;

    protected string $nickname;

    protected Carbon $dateOrBirth;

    protected string $address;

    protected Province $province;

    protected District $district;

    protected Subdistrict $subdistrict;

    protected string $postalCode;

    protected Province $registeredProvince;

    protected string $phone;

    protected string $email;

    protected FamilyStatus $familyStatus;

    protected MilitaryStatus $militaryService;

    protected EducationStatus $education;

    protected string $major;

    protected string $institution;

    protected string $gpa;

    protected array $resume;

    protected array $transcript;

    protected array $certificates;

    protected array $photo;

    /**
     * Create a new message instance.
     */
    public function __construct(Career $career, array $data)
    {
        $this->career = $career;
        $this->salary = $data['salary'];
        $this->title = UserTitle::from($data['title']);
        $this->firstNameTH = $data['first_name_th'];
        $this->lastNameTH = $data['last_name_th'];
        $this->firstNameEN = $data['first_name_en'];
        $this->lastNameEN = $data['last_name_en'];
        $this->nickname = $data['nick_name'];
        $this->dateOrBirth = Carbon::parse($data['date_of_birth']);
        $this->address = $data['address'];
        $this->province = Province::find($data['province_id']);
        $this->district = District::find($data['district_id']);
        $this->subdistrict = Subdistrict::find($data['sub_district_id']);
        $this->postalCode = $data['postal_code'];
        $this->registeredProvince = Province::find($data['registered_province_id']);
        $this->phone = $data['phone'];
        $this->email = $data['email'];
        $this->familyStatus = FamilyStatus::from($data['family_status']);
        $this->militaryService = MilitaryStatus::from($data['military_service']);
        $this->education = EducationStatus::from($data['education']);
        $this->major = $data['major'];
        $this->institution = $data['institution'];
        $this->gpa = $data['gpa'];
        $this->resume = $data['resume_file'];
        $this->transcript = $data['transcript_file'];
        $this->certificates = $data['certificate_files'];
        $this->photo = $data['photo'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Job Application from $this->firstNameTH $this->lastNameTH",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.job-application',
            with: [
                'careerTH' => $this->career->getTranslation('title', 'th'),
                'careerEN' => $this->career->getTranslation('title', 'en'),
                'salary' => number_format($this->salary, 2),
                'titleTH' => $this->title->labelTh(),
                'titleEN' => $this->title->labelEn(),
                'firstNameTH' => $this->firstNameTH,
                'lastNameTH' => $this->lastNameTH,
                'firstNameEN' => Str::upper($this->firstNameEN),
                'lastNameEN' => Str::upper($this->lastNameEN),
                'nickname' => $this->nickname,
                'dateOrBirth' => $this->dateOrBirth->format('d/m/Y'),
                'address' => $this->address,
                'provinceTH' => $this->province->name_th,
                'provinceEN' => $this->province->name_en,
                'districtTH' => $this->district->name_th,
                'districtEN' => $this->district->name_en,
                'subdistrictTH' => $this->subdistrict->name_th,
                'subdistrictEN' => $this->subdistrict->name_en,
                'postalCode' => $this->postalCode,
                'registeredProvinceTH' => $this->registeredProvince->name_th,
                'registeredProvinceEN' => $this->registeredProvince->name_en,
                'phone' => $this->phone,
                'email' => $this->email,
                'familyStatusTH' => $this->familyStatus->labelTh(),
                'familyStatusEN' => $this->familyStatus->labelEn(),
                'militaryServiceTH' => $this->militaryService->labelTh(),
                'militaryServiceEN' => $this->militaryService->labelEn(),
                'educationTH' => $this->education->labelTh(),
                'educationEN' => $this->education->labelEn(),
                'major' => $this->major,
                'institution' => $this->institution,
                'gpa' => $this->gpa,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $fullName = "$this->firstNameTH $this->lastNameTH";

        $attachments = [];

        $attachments[] = Attachment::fromStorageDisk(config('filesystems.temporary_disk'), $this->resume['path'])
            ->as($this->buildFileNameWithExtension("ประวัติส่วนตัว (Resume) ของ $fullName", $this->resume['extension']))
            ->withMime($this->resume['mime']);

        $attachments[] = Attachment::fromStorageDisk(config('filesystems.temporary_disk'), $this->transcript['path'])
            ->as($this->buildFileNameWithExtension("สำเนาวุฒิการศึกษา (Transcript) ของ $fullName", $this->transcript['extension']))
            ->withMime($this->transcript['mime']);

        $attachments[] = Attachment::fromStorageDisk(config('filesystems.temporary_disk'), $this->photo['path'])
            ->as($this->buildFileNameWithExtension("ภาพถ่าย (Photo) ของ $fullName", $this->photo['extension']))
            ->withMime($this->photo['mime']);

        foreach ($this->certificates as $index => $certificate) {
            $itemIndex = $index + 1;
            $attachments[] = Attachment::fromStorageDisk(config('filesystems.temporary_disk'), $certificate['path'])
                ->as($this->buildFileNameWithExtension("ใบประกาศต่างๆ หรือผลสอบต่างๆ ที่เกี่ยวข้อง (Certificate) ของ $fullName ($itemIndex)", $certificate['extension']))
                ->withMime($certificate['mime']);
        }

        return $attachments;
    }

    protected function buildFileNameWithExtension(string $name, string $extension): string
    {
        return $name.".{$extension}";
    }
}
