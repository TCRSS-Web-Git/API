<?php

namespace App\Http\Resources;

use App\Enums\ProductAndServiceStatus;
use App\Models\ProductAndService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductAndService
 * */
class ProductAndServiceResource extends JsonResource
{
    public function __construct(ProductAndService $resource)
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
        $imageCover = $this->getMedia(ProductAndService::MEDIA_COLLECTION_COVER)->first();
        $images = $this->getMedia(ProductAndService::MEDIA_COLLECTION_FILE)->first();

        return [
            'id' => $this->hashid,
            'cover' => $imageCover ? new MediaResource($imageCover, ProductAndService::MEDIA_COLLECTION_COVER.'_optimized') : null,
            'file' => $images ? new MediaResource($images, ProductAndService::MEDIA_COLLECTION_FILE.'_optimized') : null,
            'title' => $this->getTranslation('title'),
            'translations' => $this->when($includeTranslations, $this->getAllTranslations()),
            'status' => $this->published_at && $this->published_at <= now() ? ProductAndServiceStatus::PUBLISHED : ProductAndServiceStatus::DRAFT,
            'updated_by' => $this->latestAudit ? new MiniUserResource($this->latestAudit->user) : null,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order' => $this->order,
        ];
    }
}
