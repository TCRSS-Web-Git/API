<?php

use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dev-api', function (Request $request) {
    return response()->json(['message' => 'this is dev environtment']);
});

Route::get('/test-contact-us-mail', function (Request $request) {
    $data = [
        'name' => 'Taylor',
        'surname' => 'Swift',
        'phone' => '(+66) 081 111 1111',
        'email' => 'aaa@gmail.com',
        'department_type' => 'HR and Recruit',
        'detail' => 'I want to ask about this contact us',
    ];

    return (new ContactUs($data))->render();
});
