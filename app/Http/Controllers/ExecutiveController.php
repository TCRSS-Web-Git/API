<?php

namespace App\Http\Controllers;

use App\Actions\ReorderExecutive;
use App\Actions\SaveExecutive;
use App\Enums\ExecutiveStatus;
use App\Filters\ExecutiveFilter;
use App\Http\Requests\CreateOrUpdateExecutiveRequest;
use App\Http\Requests\ReorderExecutiveRequest;
use App\Http\Resources\ExecutiveResource;
use App\Models\Executive;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permission;
use Illuminate\Http\Request;

class ExecutiveController extends Controller
{
    /**
     * Get all executives
     *
     * @group Executives
     */
    public function index(ExecutiveFilter $filter)
    {
        if (request()->routeIs('public.executives.index')) {
            request()->query->add(['status' => ExecutiveStatus::PUBLISHED->value]);
        }

        $count = Executive::count();

        return ExecutiveResource::collection(Executive::with(['createdBy', 'updatedBy', 'publishedBy'])->filter($filter)->orderBy('order')->paginate($this->getPerPage(max($count, 1), max($count, 1))));
    }

    /**
     * Get executive by ID
     *
     * @group Executives
     */
    public function show(Executive $executive)
    {
        $executive->load(['createdBy', 'updatedBy', 'publishedBy']);

        return new ExecutiveResource($executive);
    }

    /**
     * Create executive
     *
     * @group Executives
     */
    public function store(CreateOrUpdateExecutiveRequest $request, SaveExecutive $saveExecutive)
    {
        Gate::authorize('create', Executive::class);

        $data = $request->validated();

        if (isset($data['published_at']) && $data['published_at'] != null && $request->user()->cannot(Permission::EXECUTIVE_PUBLISH, Executive::class)) {
            abort(403, 'You do not have permission to create a published executive.');
        }

        $executive = $saveExecutive->execute(new Executive, $data);

        return new ExecutiveResource($executive);
    }

    /**
     * Update executive
     *
     * @group Executives
     */
    public function update(CreateOrUpdateExecutiveRequest $request, Executive $executive, SaveExecutive $saveExecutive)
    {
        Gate::authorize('update', $executive);

        $data = $request->validated();

        if (isset($data['published_at']) && $data['published_at'] != null && $request->user()->cannot(Permission::EXECUTIVE_PUBLISH, $executive)) {
            abort(403, 'You do not have permission to update a published executive.');
        }

        $executive = $saveExecutive->execute($executive, $data);

        return new ExecutiveResource($executive);
    }

    /**
     * Delete executive
     *
     * @group Executives
     */
    public function destroy(Request $request, Executive $executive)
    {
        Gate::authorize('delete', $executive);

        if ($executive->published_at != null && $request->user()->cannot(Permission::EXECUTIVE_PUBLISH, $executive)) {
            abort(403, 'You do not have permission to delete a published executive.');
        }

        $executive->delete();

        return response()->noContent();
    }

    /**
     * Reorder executives
     *
     * @group Executives
     */
    public function reorder(ReorderExecutiveRequest $request, ReorderExecutive $reorderExecutive)
    {
        Gate::authorize('reorder', Executive::class);

        $reorderExecutive->execute($request->validated());

        return response()->json();
    }
}
