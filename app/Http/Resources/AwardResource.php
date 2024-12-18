<?php

namespace App\Http\Resources;

use App\Enums\AwardStatus;
use App\Models\Award;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Award */
class AwardResource extends JsonResource
{
    public function __construct(Award $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $includeTranslations = $request->query('include') === 'translations';

        return [
            'id' => $this->hashid,
            'title' => $this->getTranslation('title'),
            'description' => $this->getTranslation('description'),
            'description_images' => $this->when(! $request->routeIs(['awards.index']), function () {
                $images = $this->getMedia(Award::MEDIA_COLLECTION_DESCRIPTION_PHOTO);

                return $images->count() ? new MediaResourceCollection($images, Award::MEDIA_COLLECTION_DESCRIPTION_PHOTO.'_optimized') : [];
            }),
            'translations' => $this->when($includeTranslations, $this->getAllTranslations()),
            'status' => $this->published_at && $this->published_at <= now() ? AwardStatus::PUBLISHED : AwardStatus::DRAFT,
            'published_at' => $this->published_at,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->latestAudit ? new MiniUserResource($this->latestAudit->user) : null,
        ];
    }
}
