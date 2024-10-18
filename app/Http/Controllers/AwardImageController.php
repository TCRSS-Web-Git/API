<?php

namespace App\Http\Controllers;

use App\Actions\ReorderAwardImage;
use App\Actions\SaveAwardImage;
use App\Http\Requests\CreateAwardImageRequest;
use App\Http\Requests\ReorderAwardImageRequest;
use App\Http\Resources\AwardImageResource;
use App\Models\AwardImage;
use Illuminate\Support\Facades\Gate;

class AwardImageController extends Controller
{
    /**
     * Get all award images.
     *
     * @group Award images
     */
    public function index()
    {
        Gate::authorize('viewAny', AwardImage::class);

        $count = AwardImage::count();

        $awardImages = AwardImage::with(['latestAudit.user'])->orderBy('order', 'asc')->paginate($this->getPerPage($count, $count));

        return AwardImageResource::collection($awardImages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @group Award images
     */
    public function store(CreateAwardImageRequest $request, SaveAwardImage $saveAwardImage)
    {
        Gate::authorize('create', AwardImage::class);

        $awardImage = $saveAwardImage->execute(new AwardImage, $request->validated());

        return new AwardImageResource($awardImage);
    }

    /**
     * Update a resource in storage.
     *
     * @group Award images
     */
    public function update(CreateAwardImageRequest $request, SaveAwardImage $saveAwardImage, AwardImage $awardImage)
    {
        Gate::authorize('update', $awardImage);

        $awardImage = $saveAwardImage->execute($awardImage, $request->validated());

        return new AwardImageResource($awardImage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @group Award images
     */
    public function destroy(AwardImage $awardImage)
    {
        Gate::authorize('delete', $awardImage);

        $awardImage->delete();

        return response()->noContent();
    }

    /**
     * Reorder award images.
     *
     * @group Award images
     */
    public function reorder(ReorderAwardImageRequest $request, ReorderAwardImage $reorderAwardImage)
    {
        Gate::authorize('reorder', AwardImage::class);

        $reorderAwardImage->execute($request->validated());

        return response()->json();
    }
}
