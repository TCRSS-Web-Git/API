<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dev-api', function (Request $request) {
    return response()->json(['message' => 'this is dev environtment']);
});

Route::get('/test-job-mail', function (Request $request) {
    $career = \App\Models\Career::first();
    $data = [
        'salary' => 123_456_789.34,
        'title' => \App\Enums\UserTitle::MR,
        'first_name_th' => 'ชื่อ',
        'last_name_th' => 'นามสกุล',
        'first_name_en' => 'firstname',
        'last_name_en' => 'lastname',
        'nickname' => 'ชื่อเล่นน',
        'date_of_birth' => '1989-12-13',
        'address' => 'ที่อยู่ อยู่บ้าน',
        'phone' => '0942343434',
        'email' => 'test@email.com',
        'family_status' => \App\Enums\FamilyStatus::SINGLE,
        'military_service' => \App\Enums\MilitaryStatus::CONSCRIPTED,
        'education' => \App\Enums\EducationStatus::BACHELOR_DEGREE,
        'major' => 'Engineer',
        'institution' => 'KMITL',
        'gpa' => '3.59',
    ];

    return (new \App\Mail\JobApplication($career, $data))->render();
});

Route::get('/test-job-mail-sent', function (Request $request) {
    $career = \App\Models\Career::first();
    $data = [
        'salary' => 123_456_789.34,
        'title' => \App\Enums\UserTitle::MR,
        'first_name_th' => 'ชื่อ',
        'last_name_th' => 'นามสกุล',
        'first_name_en' => 'firstname',
        'last_name_en' => 'lastname',
        'nickname' => 'ชื่อเล่นน',
        'date_of_birth' => '1989-12-13',
        'address' => 'ที่อยู่ อยู่บ้าน',
        'phone' => '0942343434',
        'email' => 'test@email.com',
        'family_status' => \App\Enums\FamilyStatus::SINGLE,
        'military_service' => \App\Enums\MilitaryStatus::CONSCRIPTED,
        'education' => \App\Enums\EducationStatus::BACHELOR_DEGREE,
        'major' => 'Engineer',
        'institution' => 'KMITL',
        'gpa' => '3.59',
    ];
    \Illuminate\Support\Facades\Mail::to('test@test.com')->send(new \App\Mail\JobApplication($career, $data));

    return '';
});
