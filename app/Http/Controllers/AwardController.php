<?php

namespace App\Http\Controllers;

use App\Actions\ReorderAward;
use App\Actions\SaveAward;
use App\Filters\AwardFilter;
use App\Http\Requests\CreateOrUpdateAwardRequest;
use App\Http\Requests\ReorderAwardRequest;
use App\Http\Resources\AwardResource;
use App\Models\Award;
use Illuminate\Support\Facades\Gate;

class AwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @group Awards
     */
    public function index(AwardFilter $filter)
    {
        Gate::authorize('viewAny', Award::class);

        $count = Award::count();

        return AwardResource::collection(Award::with(['latestAudit.user'])->filter($filter)->paginate($this->getPerPage($count, $count)));
    }

    /**
     * Display the specified resource.
     *
     * @group Awards
     */
    public function show(Award $award)
    {
        return new AwardResource($award);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @group Awards
     */
    public function store(CreateOrUpdateAwardRequest $request, SaveAward $saveAward)
    {
        Gate::authorize('create', Award::class);

        $award = $saveAward->execute(new Award, $request->validated());

        return new AwardResource($award);
    }

    /**
     * Update the specified resource in storage.
     *
     * @group Awards
     */
    public function update(CreateOrUpdateAwardRequest $request, Award $award, SaveAward $saveAward)
    {
        Gate::authorize('update', $award);

        $award = $saveAward->execute($award, $request->validated());

        return new AwardResource($award);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @group Awards
     */
    public function destroy(Award $award)
    {
        Gate::authorize('delete', $award);

        $award->delete();

        return response()->noContent();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @group Product and services
     */
    public function reorder(ReorderAwardRequest $request, ReorderAward $reorderAward)
    {
        Gate::authorize('reorder', Award::class);

        $reorderAward->execute($request->validated());

        return response()->json();
    }
}
