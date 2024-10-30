<?php

namespace App\Http\Controllers;

use App\Actions\SaveCareer;
use App\Enums\CareerStatus;
use App\Filters\CareerFilter;
use App\Http\Requests\CreateOrUpdateCareerRequest;
use App\Http\Resources\CareerResource;
use App\Models\Career;
use Illuminate\Support\Facades\Gate;

class CareerController extends Controller
{
    /**
     * Get all career.
     *
     * @group Careers
     */
    public function index(CareerFilter $filter)
    {
        if (request()->routeIs('public.careers.index')) {
            request()->query->add(['status' => CareerStatus::PUBLISHED->value]);
        }

        return CareerResource::collection(Career::with(['type', 'location', 'department', 'latestAudit.user'])->filter($filter)->paginate($this->getPerPage()));
    }

    /**
     * Create a career.
     *
     * @group Careers
     */
    public function store(CreateOrUpdateCareerRequest $request, SaveCareer $saveCareer)
    {
        Gate::authorize('create', Career::class);

        $career = $saveCareer->execute(new Career, $request->validated());

        return new CareerResource($career);
    }

    /**
     * Get a career by id.
     *
     * @group Careers
     */
    public function show(Career $career)
    {
        return new CareerResource($career);
    }

    /**
     * Update a career.
     *
     * @group Careers
     */
    public function update(Career $career, CreateOrUpdateCareerRequest $request, SaveCareer $saveCareer)
    {
        Gate::authorize('update', $career);

        $career = $saveCareer->execute($career, $request->validated());

        return new CareerResource($career);
    }

    /**
     * Delete a career.
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
