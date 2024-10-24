<?php

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
        'department' => 'HR and Recruit',
        'detail' => 'I want to ask about this application job',
    ];

    return (new \App\Mail\ContactUs($data))->render();
});
