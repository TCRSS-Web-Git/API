<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobApplicationRequest;
use App\Mail\JobApplication;
use App\Models\Career;
use Illuminate\Support\Facades\Mail;

class JobApplicationController extends Controller
{
    public function create(CreateJobApplicationRequest $request)
    {
        $data = $request->validated();
        $career = Career::find($data['career_id']);

        // TODO: get user for sent mail: ส่งให้ admin และ superadmin
        Mail::to('TODO')->send(new JobApplication($career, $data));
    }
}
