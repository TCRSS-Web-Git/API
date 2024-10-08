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
     * Create a job post.
     *
     * @group Careers
     */
    public function store(CreateOrUpdateJobPostRequest $request, SaveJobPost $saveJobPost)
    {
        Gate::authorize('create', JobPost::class);

        $jobPost = $saveJobPost->execute(new JobPost, $request->validated());

        return new JobPostResource($jobPost);
    }

    /**
     * Get a job post by id.
     *
     * @group Careers
     */
    public function show(JobPost $career)
    {
        return new JobPostResource($career);
    }

    /**
     * Update a job post.
     *
     * @group Careers
     */
    public function update(JobPost $career, CreateOrUpdateJobPostRequest $request, SaveJobPost $saveJobPost)
    {
        Gate::authorize('update', $career);

        $career = $saveJobPost->execute(new JobPost, $request->validated());

        return new JobPostResource($career);
    }

    /**
     * Delete a job post.
     *
     * @group Careers
     */
    public function destroy(JobPost $career)
    {
        Gate::authorize('delete', $career);

        $career->delete();

        return response()->noContent();
    }
}
