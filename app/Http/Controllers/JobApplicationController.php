<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobApplicationRequest;
use App\Mail\JobApplication;
use App\Models\Career;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class JobApplicationController extends Controller
{
    public function create(CreateJobApplicationRequest $request)
    {
        $data = $request->validated();
        $career = Career::find($data['career_id']);

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [Role::ROLE_SUPER_ADMIN, Role::ROLE_ADMIN]);
        })
            ->get();

        Mail::to($users)->queue(new JobApplication($career, $data));

        return response(null, Response::HTTP_CREATED);
    }
}
