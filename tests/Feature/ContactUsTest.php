<?php

namespace Tests\Feature;

use App\Enums\DepartmentType;
use App\Mail\ContactUs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactUsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_contact_us_form(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

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
        Mail::assertQueued(ContactUs::class, function (ContactUs $mail) use ($superAdmin, $admin, $name, $surname) {
            return $mail->hasTo($superAdmin->email) &&
                $mail->hasTo($admin->email) &&
                $mail->hasSubject("Contact Us From $name $surname");
        });
    }
}
