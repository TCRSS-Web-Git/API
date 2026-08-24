<?php

namespace App\Http\Resources;

use App\Enums\BoardDirectorStatus;
use App\Models\BoardDirector;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BoardDirector
 */
class BoardDirectorResource extends JsonResource
{
    public function __construct(BoardDirector $resource)
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
        $image = $this->getMedia(BoardDirector::MEDIA_COLLECTION_IMAGE)->first();

        return [
            'id' => $this->hashid,
            'image' => $image ? new MediaResource($image, BoardDirector::MEDIA_COLLECTION_IMAGE.'_optimized') : null,
            'name' => $this->getTranslation('name'),
            'position' => $this->getTranslation('position'),
            'translations' => $this->when($includeTranslations, $this->getAllTranslations()),
            'created_by' => $this->createdBy ? new MiniUserResource($this->createdBy) : null,
            'updated_by' => $this->updatedBy ? new MiniUserResource($this->updatedBy) : null,
            'status' => $this->published_at && $this->published_at <= now() ? BoardDirectorStatus::PUBLISHED : BoardDirectorStatus::DRAFT,
            'published_at' => $this->published_at,
            'published_by' => $this->publishedBy ? new MiniUserResource($this->publishedBy) : null,
            'group_order' => $this->group_order,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
