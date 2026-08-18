<?php

namespace App\Http\Resources;

use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Popup */
class PopupResource extends JsonResource
{
    public function __construct(Popup $resource)
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
        $image = $this->getMedia(Popup::MEDIA_COLLECTION_IMAGE)->first();

        return [
            'id' => $this->hashid,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => $image ? new MediaResource($image, Popup::MEDIA_COLLECTION_IMAGE.'_optimized') : null,
        ];
    }
}
