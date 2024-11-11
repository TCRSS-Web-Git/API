<?php

namespace App\Http\Resources;

use App\Models\AwardImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AwardImage */
class AwardImageResource extends JsonResource
{
    public function __construct(AwardImage $resource)
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
        $image = $this->getMedia(AwardImage::MEDIA_COLLECTION_IMAGE)->first();

        return [
            'id' => $this->hashid,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => $image ? new MediaResource($image, AwardImage::MEDIA_COLLECTION_IMAGE.'_optimized') : null,
        ];
    }
}
