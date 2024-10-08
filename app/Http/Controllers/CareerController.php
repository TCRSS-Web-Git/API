<?php

namespace App\Http\Controllers;

use App\Actions\SaveCareer;
use App\Filters\CareerFilter;
use App\Http\Requests\CreateOrUpdateCareerRequest;
use App\Http\Resources\CareerResource;
use App\Models\Career;
use Illuminate\Support\Facades\Gate;

class CareerController extends Controller
{
    /**
     * Get all job posts
     *
     * @group Careers
     */
    public function index(CareerFilter $filter)
    {
        return CareerResource::collection(Career::with(['type', 'location', 'department', 'latestAudit.user'])->filter($filter)->paginate($this->getPerPage()));
    }

    /**
     * Create a job post.
     *
     * @group Careers
     */
    public function store(CreateOrUpdateCareerRequest $request, SaveCareer $saveCareer)
    {
        Gate::authorize('create', Career::class);

        $jobPost = $saveCareer->execute(new Career, $request->validated());

        return new CareerResource($jobPost);
    }

    /**
     * Get a job post by id.
     *
     * @group Careers
     */
    public function show(Career $career)
    {
        return new CareerResource($career);
    }

    /**
     * Update a job post.
     *
     * @group Careers
     */
    public function update(Career $career, CreateOrUpdateCareerRequest $request, SaveCareer $saveCareer)
    {
        Gate::authorize('update', $career);

        $career = $saveCareer->execute(new Career, $request->validated());

        return new CareerResource($career);
    }

    /**
     * Delete a job post.
     *
     * @group Careers
     */
    public function destroy(Career $career)
    {
        Gate::authorize('delete', $career);

        $career->delete();

        return response()->noContent();
    }
}
