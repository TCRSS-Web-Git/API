<?php

namespace App\Http\Controllers;

use App\Actions\ReorderBoardDirector;
use App\Actions\SaveBoardDirector;
use App\Enums\BoardDirectorStatus;
use App\Filters\BoardDirectorFilter;
use App\Http\Requests\CreateOrUpdateBoardDirectorRequest;
use App\Http\Requests\ReorderBoardDirectorRequest;
use App\Http\Resources\BoardDirectorResource;
use App\Models\BoardDirector;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permission;
use Illuminate\Http\Request;

class BoardDirectorController extends Controller
{
    /**
     * Get all board directors
     *
     * @group Board of Directors
     */
    public function index(BoardDirectorFilter $filter)
    {
        if (request()->routeIs('public.board-directors.index')) {
            request()->query->add(['status' => BoardDirectorStatus::PUBLISHED->value]);
        }

        $count = BoardDirector::count();

        return BoardDirectorResource::collection(BoardDirector::with(['createdBy', 'updatedBy', 'publishedBy'])->filter($filter)->orderBy('order')->paginate($this->getPerPage(max($count, 1), max($count, 1))));
    }

    /**
     * Get board director by ID
     *
     * @group Board of Directors
     */
    public function show(BoardDirector $boardDirector)
    {
        $boardDirector->load(['createdBy', 'updatedBy', 'publishedBy']);

        return new BoardDirectorResource($boardDirector);
    }

    /**
     * Create board director
     *
     * @group Board of Directors
     */
    public function store(CreateOrUpdateBoardDirectorRequest $request, SaveBoardDirector $saveBoardDirector)
    {
        Gate::authorize('create', BoardDirector::class);

        $data = $request->validated();

        if (isset($data['published_at']) && $data['published_at'] != null && $request->user()->cannot(Permission::BOARDDIRECTOR_PUBLISH, BoardDirector::class)) {
            abort(403, 'You do not have permission to create a published board director.');
        }

        $boardDirector = $saveBoardDirector->execute(new BoardDirector, $data);

        return new BoardDirectorResource($boardDirector);
    }

    /**
     * Update board director
     *
     * @group Board of Directors
     */
    public function update(CreateOrUpdateBoardDirectorRequest $request, BoardDirector $boardDirector, SaveBoardDirector $saveBoardDirector)
    {
        Gate::authorize('update', $boardDirector);

        $data = $request->validated();

        if (isset($data['published_at']) && $data['published_at'] != null && $request->user()->cannot(Permission::BOARDDIRECTOR_PUBLISH, $boardDirector)) {
            abort(403, 'You do not have permission to update a published board director.');
        }

        $boardDirector = $saveBoardDirector->execute($boardDirector, $data);

        return new BoardDirectorResource($boardDirector);
    }

    /**
     * Delete board director
     *
     * @group Board of Directors
     */
    public function destroy(Request $request, BoardDirector $boardDirector)
    {
        Gate::authorize('delete', $boardDirector);

        if ($boardDirector->published_at != null && $request->user()->cannot(Permission::BOARDDIRECTOR_PUBLISH, $boardDirector)) {
            abort(403, 'You do not have permission to delete a published board director.');
        }

        $boardDirector->delete();

        return response()->noContent();
    }

    /**
     * Reorder board directors
     *
     * @group Board of Directors
     */
    public function reorder(ReorderBoardDirectorRequest $request, ReorderBoardDirector $reorderBoardDirector)
    {
        Gate::authorize('reorder', BoardDirector::class);

        $reorderBoardDirector->execute($request->validated());

        return response()->json();
    }
}
