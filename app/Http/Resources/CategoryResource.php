<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Category
 */
class CategoryResource extends JsonResource
{
    public function __construct(Category $resource)
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
        return [
            'id' => $this->hashid,
            'name' => $this->getTranslation('name'),
            'description' => $this->when($request->routeIs(['categories.*']), $this->getTranslation('description')),
            'slug' => $this->slug,
            'sort' => $this->when($request->routeIs(['categories.*']), $this->sort),
            'created_at' => $this->when($request->routeIs(['categories.*']), $this->created_at),
            'updated_at' => $this->when($request->routeIs(['categories.*']), $this->updated_at),
        ];
    }
}
