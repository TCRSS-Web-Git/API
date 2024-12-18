<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin Media
 */
class MediaResource extends JsonResource
{
    protected $conversionName;

    public function __construct(Media $resource, $conversionName = 'optimized')
    {
        parent::__construct($resource);
        $this->conversionName = $conversionName;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->file_name,
            'url' => $this->hasGeneratedConversion($this->conversionName) ? $this->getFullUrl($this->conversionName) : $this->getFullUrl(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
