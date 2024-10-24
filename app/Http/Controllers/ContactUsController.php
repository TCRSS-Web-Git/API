<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateContactUsRequest;
use App\Mail\ContactUs;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function create(CreateContactUsRequest $request)
    {
        $data = $request->validated();
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [Role::ROLE_SUPER_ADMIN, Role::ROLE_ADMIN]);
        })
            ->get();

        Mail::to($users)->queue(new ContactUs($data));

        return response(null, Response::HTTP_CREATED);
    }
}
