<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobApplicationRequest;
use App\Mail\JobApplication;
use App\Models\Career;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class JobApplicationController extends Controller
{
    public function create(CreateJobApplicationRequest $request)
    {
        $data = $request->validated();
        $career = Career::find($data['career_id']);

        foreach (config('tcrss.mails_for_job_application') as $email) {
            Mail::to($email)->queue(new JobApplication($career, $data));
        }

        return response(null, Response::HTTP_CREATED);
    }
}
