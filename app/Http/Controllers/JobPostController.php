<?php

namespace App\Http\Controllers;

use App\Actions\SaveJobPost;
use App\Filters\JobPostFilter;
use App\Http\Requests\CreateOrUpdateJobPostRequest;
use App\Http\Resources\JobPostResource;
use App\Models\JobPost;
use Illuminate\Support\Facades\Gate;

class JobPostController extends Controller
{
    /**
     * Get all job posts
     *
     * @group Careers
     */
    public function index(JobPostFilter $filter)
    {
        return JobPostResource::collection(JobPost::with(['location', 'department', 'latestAudit.user'])->filter($filter)->paginate($this->getPerPage()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrUpdateJobPostRequest $request, SaveJobPost $saveJobPost)
    {
        Gate::authorize('create', JobPost::class);

        $jobPost = $saveJobPost->execute(new JobPost, $request->validated());

        return new JobPostResource($jobPost);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPost $career)
    {
        return new JobPostResource($career);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateOrUpdateJobPostRequest $request, SaveJobPost $saveJobPost, JobPost $jobPost)
    {
        Gate::authorize('update', $jobPost);

        $jobPost = $saveJobPost->execute(new JobPost, $request->validated());

        return new JobPostResource($jobPost);
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
