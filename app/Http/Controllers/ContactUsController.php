<?php

namespace App\Http\Controllers;

use App\Enums\DepartmentType;
use App\Http\Requests\CreateContactUsRequest;
use App\Mail\ContactUs;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function create(CreateContactUsRequest $request)
    {
        $data = $request->validated();

        $mails = DepartmentType::from($data['department_type'])->email();
        foreach ($mails as $email) {
            Mail::to($email)->queue(new ContactUs($data));
        }

        return response(null, Response::HTTP_CREATED);
    }
}
