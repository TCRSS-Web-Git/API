<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MediaResourceCollection extends ResourceCollection
{
    protected $conversionName;

    public function __construct($resource, $conversionName = null)
    {
        parent::__construct($resource);
        $this->conversionName = $conversionName;
    }

    public function toArray($request)
    {
        return $this->collection->map(function ($mediaResource) use ($request) {
            return (new MediaResource($mediaResource->resource, $this->conversionName))->toArray($request);
        })->all();
    }
}
