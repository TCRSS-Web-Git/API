<?php

namespace App\Filters;

use App\Models\Category;
use App\Models\JobPost;

class JobPostFilter extends QueryFilter
{
    protected $sortable = [
        'id',
        'title',
        'department_id',
        'location_id',
        'type',
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
            return JobPost::decodeHash(trim($id));
        }, $ids);
        $ids = array_filter($ids, static function ($id) {
            return $id > 0;
        });

        if (empty($ids)) {
            return $this->builder;
        }

        return $this->builder->whereIn('id', $ids);
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
            $query->whereRaw('LOWER(meta_title) LIKE ?', ['%'.strtolower($value).'%']);
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

    public function location_id($value)
    {
        $locationIds = explode(',', $value);
        $locationIds = array_map(static function ($id) {
            return Category::decodeHash(trim($id));
        }, $locationIds);
        $locationIds = array_filter($locationIds, static function ($id) {
            return $id > 0;
        });

        if (empty($locationIds)) {
            return $this->builder;
        }

        return $this->builder->whereIn('location_id', $locationIds);
    }

    public function department_id($value)
    {
        $departmentIds = explode(',', $value);
        $departmentIds = array_map(static function ($id) {
            return Category::decodeHash(trim($id));
        }, $departmentIds);
        $departmentIds = array_filter($departmentIds, static function ($id) {
            return $id > 0;
        });

        if (empty($departmentIds)) {
            return $this->builder;
        }

        return $this->builder->whereIn('department_id', $departmentIds);
    }

    public function search($value)
    {
        // Search by hashID (support multiple hashID, comma separated)
        $ids = JobPost::decodeMultipleHashString($value);

        return $this->builder->where(function ($query) use ($value, $ids) {
            $query->whereHas('translations', function ($query) use ($value) {
                if (config('database.default') === 'mysql') {
                    $query->whereRaw('MATCH(title, body) AGAINST(? IN BOOLEAN MODE)', [$value]);
                } elseif (config('database.default') === 'sqlite') {
                    $query->where('title', 'like', '%'.$value.'%');
                    $query->orWhere('body', 'like', '%'.$value.'%');
                }
            })
                ->when($ids, function ($query) use ($ids) {
                    $query->orWhereIn('id', $ids);
                });
        });
    }
}
