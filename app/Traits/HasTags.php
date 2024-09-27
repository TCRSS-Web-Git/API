<?php

namespace App\Traits;

use App\Models\Tag;
use ArrayAccess;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;

trait HasTags
{
    public function getTagType(): ?string
    {
        return class_basename(static::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
    }

    public function syncTags(string|array|ArrayAccess $tags): static
    {
        if (is_string($tags)) {
            $tags = Arr::wrap($tags);
        }

        $tags = collect(Tag::findOrCreate($tags, $this->getTagType()));

        $this->syncTagIds($tags->pluck('id')->toArray(), $this->getTagType());

        return $this;
    }

    protected function syncTagIds($ids, ?string $type = null, $detaching = true): void
    {
        $isUpdated = false;

        // Get a list of tag_ids for all current tags
        $current = $this->tags()
            ->newPivotStatement()
            ->where('taggable_id', $this->getKey())
            ->where('taggable_type', static::class)
            ->when($type !== null, function ($query) use ($type) {
                $tagModel = $this->tags()->getRelated();

                return $query->join(
                    $tagModel->getTable(),
                    'taggables.tag_id',
                    '=',
                    $tagModel->getTable().'.'.$tagModel->getKeyName()
                )
                    ->where($tagModel->getTable().'.type', $type);
            })
            ->pluck('tag_id')
            ->all();

        // Compare to the list of ids given to find the tags to remove
        $detach = array_diff($current, $ids);
        if ($detaching && count($detach) > 0) {
            $this->tags()->detach($detach);
            $isUpdated = true;
        }

        // Attach any new ids
        $attach = array_unique(array_diff($ids, $current));
        if (count($attach) > 0) {
            collect($attach)->each(function ($id) {
                $this->tags()->attach($id, []);
            });
            $isUpdated = true;
        }

        // Once we have finished attaching or detaching the records, we will see if we
        // have done any attaching or detaching, and if we have we will touch these
        // relationships if they are configured to touch on any database updates.
        if ($isUpdated) {
            $this->tags()->touchIfTouching();
            // delete tags that are not used by any model
            Tag::where('type', $this->getTagType())->doesntHave('taggables')->delete();
        }
    }
}
