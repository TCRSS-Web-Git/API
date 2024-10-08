<?php

namespace App\Http\Resources;

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin JobPost
 */
class JobPostResource extends JsonResource
{
    public function __construct(JobPost $resource)
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

        return [
            'id' => $this->hashid,
            'body_images' => $this->when(! $request->routeIs(['careers.index']), function () {
                $images = $this->getMedia(JobPost::MEDIA_COLLECTION_BODY_PHOTO);

                return $images->count() ? new MediaResourceCollection($images, JobPost::MEDIA_COLLECTION_BODY_PHOTO.'_optimized') : [];
            }),
            'location' => $this->location ? new CategoryResource($this->location) : null,
            'type' => $this->type->label(),
            'department' => $this->department ? new CategoryResource($this->department) : null,
            'title' => $this->getTranslation('title'),
            'body' => $this->when(! $request->routeIs(['careers.index']), $this->getTranslation('body')),
            'meta_title' => $this->getTranslation('meta_title'),
            'meta_description' => $this->getTranslation('meta_description'),
            'translations' => $this->when($includeTranslations, $this->getAllTranslations()),
            'status' => $this->published_at && $this->published_at <= now() ? 'published' : 'draft',
            'updated_by' => $this->latestAudit ? new MiniUserResource($this->latestAudit->user) : null,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
