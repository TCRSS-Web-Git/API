<?php

namespace App\Http\Controllers;

use App\Actions\SavePopup;
use App\Http\Requests\CreatePopupRequest;
use App\Http\Requests\UpdatePopupRequest;
use App\Http\Resources\PopupResource;
use App\Models\Popup;
use Illuminate\Support\Facades\Gate;

class PopupController extends Controller
{
    /**
     * Get all award images.
     *
     * @group Award images
     */
    public function index()
    {
        Gate::authorize('viewAny', Popup::class);

        $popups = Popup::with(['latestAudit.user'])->orderBy('order', 'asc')->get();

        return PopupResource::collection($popups);
    }

    public function display()
    {
        Gate::authorize('viewAny', Popup::class);

        $popups = Popup::with(['latestAudit.user'])->where('is_active', true)->orderBy('order', 'asc')->get();

        return PopupResource::collection($popups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @group Award images
     */
    public function store(CreatePopupRequest $request, SavePopup $savePopup)
    {
        Gate::authorize('create', Popup::class);

        $popup = $savePopup->execute(new Popup, $request->validated());

        return new PopupResource($popup);
    }

    /**
     * Update a resource in storage.
     *
     * @group Award images
     */
    public function update(UpdatePopupRequest $request, SavePopup $savePopup, Popup $popup)
    {
        Gate::authorize('update', $popup);

        $popup = $savePopup->execute($popup, $request->validated());

        return new PopupResource($popup);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @group Award images
     */
    public function destroy(Popup $popup)
    {
        Gate::authorize('delete', $popup);

        $popup->delete();

        return response()->noContent();
    }
}
