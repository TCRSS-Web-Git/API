<?php

namespace App\Filters;

use App\Models\Blog;

class BlogFilter extends QueryFilter
{
    protected $sortable = [
        'id',
        'title',
        'body',
        'meta_title',
        'meta_description',
        'created_at',
        'updated_at',
    ];

    protected $translatedFields = ['title', 'body', 'meta_title', 'meta_description']; // Add translated fields here, used for sorting

    public function id($value)
    {
        $ids = explode(',', $value);
        $ids = array_map(static function ($id) {
            return Blog::decodeHash(trim($id));
        }, $ids);
        $ids = array_filter($ids, static function ($id) {
            return $id > 0;
        });

        if (empty($ids)) {
            return $this->builder;
        }

        return $this->builder->whereIn('id', $ids);
    }

    public function slug($value)
    {
        return $this->builder->whereRaw('LOWER(slug) LIKE ?', ['%'.strtolower($value).'%']);
    }

    public function title($value)
    {
        return $this->builder->whereHas('translations', function ($query) use ($value) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%'.strtolower($value).'%']);
        });
    }

    public function body($value)
    {
        return $this->builder->whereHas('translations', function ($query) use ($value) {
            $query->whereRaw('LOWER(body) LIKE ?', ['%'.strtolower($value).'%']);
        });
    }

    public function meta_title($value)
    {
        return $this->builder->whereHas('translations', function ($query) use ($value) {
            $query->whereRaw('LOWER(meta_title) LIKE ?', ['%'.strtolower($value).'%']);
        });
    }

    public function meta_description($value)
    {
        return $this->builder->whereHas('translations', function ($query) use ($value) {
            $query->whereRaw('LOWER(meta_description) LIKE ?', ['%'.strtolower($value).'%']);
        });
    }

    public function status($value)
    {
        if (strtolower($value) === 'published') {
            return $this->builder->where('published_at', '<=', now());
        }

        if (strtolower($value) === 'draft') {
            return $this->builder->where(function () {
                $this->builder->where('published_at', '>', now())
                    ->orWhere('published_at', null);
            });
        }

        return $this->builder;
    }

    public function search($value)
    {
        //        return $this->builder->where(function ($query) use ($value) {
        //            $query->where('id', $value)
        //                ->orWhere('slug', 'LIKE', '%'.strtolower($value).'%')
        //                ->orWhereHas('translations', function ($query) use ($value) {
        //                    $query->whereRaw('LOWER(title) LIKE ?', ['%'.strtolower($value).'%'])
        //                        ->orWhereRaw('LOWER(body) LIKE ?', ['%'.strtolower($value).'%'])
        //                        ->orWhereRaw('LOWER(meta_title) LIKE ?', ['%'.strtolower($value).'%'])
        //                        ->orWhereRaw('LOWER(meta_description) LIKE ?', ['%'.strtolower($value).'%']);
        //                });
        //        });

        // Search by hashID (support multiple hashID, comma separated)
        $ids = Blog::decodeMultipleHashString($value);

        return $this->builder->where(function ($query) use ($value, $ids) {
            $query->where('slug', 'LIKE', '%'.strtolower($value).'%')
                ->orWhereHas('translations', function ($query) use ($value) {
                    $query->whereRaw('MATCH(title, body) AGAINST(? IN BOOLEAN MODE)', [$value]);
                })
                ->when($ids, function ($query) use ($ids) {
                    $query->orWhereIn('id', $ids);
                });
        });
    }

    public function tags($value)
    {
        $tags = explode(',', $value);
        $tags = array_map('trim', $tags);
        $tags = array_filter($tags);

        if (empty($tags)) {
            return $this->builder;
        }

        return $this->builder->withAnyTags($tags, Blog::getTagType());
    }
}
