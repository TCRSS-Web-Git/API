<?php

namespace App\Filters;

use App\Models\Executive;

class ExecutiveFilter extends QueryFilter
{
    protected $sortable = [
        'id',
        'name',
        'group_order',
        'order',
        'created_at',
        'updated_at',
    ];

    protected $defaultSort = ['column' => 'group_order', 'direction' => 'asc']; // primary sort; controller adds 'order' as secondary

    protected $translatedFields = ['name']; // Add translated fields here, used for sorting

    public function name($value)
    {
        return $this->builder->whereHas('translations', function ($query) use ($value) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($value).'%']);
        });
    }

    public function search($value)
    {
        // Search by hashID (support multiple hashID, comma separated)
        $ids = Executive::decodeMultipleHashString($value);

        return $this->builder->where(function ($query) use ($value, $ids) {
            $query->whereHas('translations', function ($query) use ($value) {
                $query->whereRaw('MATCH(name, position) AGAINST(? IN BOOLEAN MODE)', [$value]);
            })
                ->when($ids, function ($query) use ($ids) {
                    $query->orWhereIn('id', $ids);
                });
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
}
