<?php

namespace App\Http\Controllers;

use App\Actions\SaveJobPost;
use App\Http\Requests\CreateOrUpdateJobPostRequest;
use App\Models\JobPost;
use Illuminate\Support\Facades\Gate;

class JobPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrUpdateJobPostRequest $request, SaveJobPost $saveJobPost)
    {
        Gate::authorize('create', JobPost::class);

        $jobPost = $saveJobPost->execute(new JobPost, $request->validated());

        return $jobPost;
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPost $jobPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateOrUpdateJobPostRequest $request, SaveJobPost $saveJobPost, JobPost $jobPost)
    {
        Gate::authorize('update', $jobPost);

        $jobPost = $saveJobPost->execute(new JobPost, $request->validated());

        return $jobPost;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPost $jobPost)
    {
        Gate::authorize('delete', $jobPost);

        $jobPost->delete();

        return response()->noContent();
    }
}
