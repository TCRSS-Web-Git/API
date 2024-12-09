<?php

namespace Tests\Feature;

use App\Enums\DepartmentType;
use App\Mail\ContactUs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactUsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_contact_us_form(): void
    {
        $mail1 = 'mail1@test.com';
        $mail2 = 'mail2@test.com';
        Config::set('tcrss.mails_for_contact_us_to_customer_service', [$mail1, $mail2]);
        Mail::fake();

        $response = $this->postJson(route('public.contact_us.create'), [
            'name' => $name = 'เทย์เลอร์',
            'surname' => $surname = 'สวิฟต์',
            'department_type' => DepartmentType::CUSTOMER_SERVICE,
            'detail' => 'TAYLOR SWIFT',
            'phone' => fake()->e164PhoneNumber(),
            'email' => fake()->email(),
        ]);

        $response->assertStatus(201);
        Mail::assertQueued(ContactUs::class, function (ContactUs $mail) use ($name, $surname, $mail1) {
            return $mail->hasTo($mail1) &&
                $mail->hasSubject("Contact Us From $name $surname");
        });

        Mail::assertQueued(ContactUs::class, function (ContactUs $mail) use ($name, $surname, $mail2) {
            return $mail->hasTo($mail2) &&
                $mail->hasSubject("Contact Us From $name $surname");
        });
    }
}
