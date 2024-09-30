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
        return $this->collection->map(function ($media) use ($request) {
            return (new MediaResource($media, $this->conversionName))->toArray($request);
        })->all();
    }
}
