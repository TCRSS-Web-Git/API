<?php

namespace App\Http\Resources;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Blog
 */
class BlogResource extends JsonResource
{
    public function __construct(Blog $resource)
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
            'category' => new CategoryResource($this->category),
            'slug' => $this->slug,
            'title' => $this->getTranslation('title'),
            'body' => $this->when(! $request->routeIs(['blogs.index']), $this->getTranslation('body')),
            'meta_title' => $this->getTranslation('meta_title'),
            'meta_description' => $this->getTranslation('meta_description'),
            'status' => $this->published_at && $this->published_at <= now() ? 'published' : 'draft',
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
