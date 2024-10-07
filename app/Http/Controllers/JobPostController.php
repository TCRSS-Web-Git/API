<?php

namespace App\Http\Controllers;

use App\Filters\JobPostFilter;
use App\Http\Resources\JobPostResource;
use App\Models\JobPost;
use Illuminate\Http\Request;
use App\Actions\SaveJobPost;
use App\Http\Requests\CreateOrUpdateJobPostRequest;
use App\Http\Requests\StoreJobPostRequest;
use App\Models\Blog;
use App\Models\JobPost;
use Illuminate\Support\Facades\Gate;

class JobPostController extends Controller
{
    /**
     * Get all job posts
     *
     * @group Blogs
     */
    public function index(JobPostFilter $filter)
    {
        return JobPostResource::collection(JobPost::with(['category', 'latestAudit.user'])->filter($filter)->paginate($this->getPerPage()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrUpdateJobPostRequest $request, SaveJobPost $saveJobPost)
    {
//        Gate::authorize('create', JobPost::class);
//
//        $jobPost = $saveJobPost->execute(new JobPost, $request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPost $jobPost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobPost $jobPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateOrUpdateJobPostRequest $request, JobPost $jobPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPost $jobPost)
    {
        //
    }
}
