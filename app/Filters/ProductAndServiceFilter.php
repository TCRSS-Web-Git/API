<?php

namespace App\Filters;

use App\Models\ProductAndService;

class ProductAndServiceFilter extends QueryFilter
{
    protected $sortable = [
        'id',
        'title',
        'order',
        'created_at',
        'updated_at',
    ];

    protected $defaultSort = ['column' => 'order', 'direction' => 'desc']; // Edit default sorting here

    protected $translatedFields = ['title']; // Add translated fields here, used for sorting

    public function title($value)
    {
        return $this->builder->whereHas('translations', function ($query) use ($value) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%'.strtolower($value).'%']);
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
        // Search by hashID (support multiple hashID, comma separated)
        $ids = ProductAndService::decodeMultipleHashString($value);

        return $this->builder->where(function ($query) use ($value, $ids) {
            $query->whereHas('translations', function ($query) use ($value) {
                $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$value]);
            })
                ->when($ids, function ($query) use ($ids) {
                    $query->orWhereIn('id', $ids);
                });
        });
    }
}
