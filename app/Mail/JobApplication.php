<?php

namespace App\Mail;

use App\Enums\EducationStatus;
use App\Enums\FamilyStatus;
use App\Enums\MilitaryStatus;
use App\Enums\UserTitle;
use App\Models\Career;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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

    // TODO province, district, subdistrict, zipcode

    protected string $phone;

    protected string $email;

    protected FamilyStatus $familyStatus;

    protected MilitaryStatus $militaryService;

    protected EducationStatus $education;

    protected string $major;

    protected string $institution;

    protected string $gpa;

    /**
     * Create a new message instance.
     */
    public function __construct(Career $career, array $data)
    {
        $this->career = $career;
        $this->salary = $data['salary'];
        $this->title = $data['title'];
        $this->firstNameTH = $data['first_name_th'];
        $this->lastNameTH = $data['last_name_th'];
        $this->firstNameEN = $data['first_name_en'];
        $this->lastNameEN = $data['last_name_en'];
        $this->nickname = $data['nickname'];
        $this->dateOrBirth = Carbon::parse($data['date_of_birth']);
        $this->address = $data['address'];
        // TODO province, district, subdistrict, zipcode
        $this->phone = $data['phone'];
        $this->email = $data['email'];
        $this->familyStatus = $data['family_status'];
        $this->militaryService = $data['military_service'];
        $this->education = $data['education'];
        $this->major = $data['major'];
        $this->institution = $data['institution'];
        $this->gpa = $data['gpa'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Application from xxx yyy', //TODO หัว email
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
                'firstNameEN' => $this->firstNameEN,
                'lastNameEN' => $this->lastNameEN,
                'nickname' => $this->nickname,
                'dateOrBirth' => $this->dateOrBirth->format('d/m/Y'),
                'address' => $this->address,
                // TODO province, district, subdistrict, zipcode
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
        // TODO แนบไฟล์
        return [];
    }
}
