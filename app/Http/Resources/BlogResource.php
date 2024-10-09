<?php

namespace App\Http\Resources;

use App\Enums\BlogStatus;
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
        $includeTranslations = $request->query('include') === 'translations';

        $imageThumbnail = $this->getMedia(Blog::MEDIA_COLLECTION_THUMBNAIL)->first();

        return [
            'id' => $this->hashid,
            'thumbnail' => $imageThumbnail ? new MediaResource($imageThumbnail, Blog::MEDIA_COLLECTION_THUMBNAIL.'_optimized') : null,
            'cover' => $this->when(! $request->routeIs(['blogs.index']), function () {
                $imageCover = $this->getMedia(Blog::MEDIA_COLLECTION_COVER)->first();

                return $imageCover ? new MediaResource($imageCover, Blog::MEDIA_COLLECTION_COVER.'_optimized') : null;
            }),
            'body_images' => $this->when(! $request->routeIs(['blogs.index']), function () {
                $images = $this->getMedia(Blog::MEDIA_COLLECTION_BODY_PHOTO);

                return $images->count() ? new MediaResourceCollection($images, Blog::MEDIA_COLLECTION_BODY_PHOTO.'_optimized') : [];
            }),
            'category' => $this->category ? new CategoryResource($this->category) : null,
            'slug' => $this->slug,
            'title' => $this->getTranslation('title'),
            'body' => $this->when(! $request->routeIs(['blogs.index']), $this->getTranslation('body')),
            'meta_title' => $this->getTranslation('meta_title'),
            'meta_description' => $this->getTranslation('meta_description'),
            'translations' => $this->when($includeTranslations, $this->getAllTranslations()),
            'status' => $this->published_at && $this->published_at <= now() ? BlogStatus::PUBLISHED : BlogStatus::DRAFT,
            'tags' => $this->tags->pluck('name'),
            'updated_by' => $this->latestAudit ? new MiniUserResource($this->latestAudit->user) : null,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
