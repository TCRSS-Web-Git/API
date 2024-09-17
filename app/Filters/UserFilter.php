<?php

namespace App\Filters;

class UserFilter extends QueryFilter
{
    protected $sortable = [
        'id',
        'email',
        'phone',
        'first_name',
        'last_name',
        'created_at',
        'updated_at',
    ];

    //    public function include($value)
    //    {
    //        return $this->builder->with($value);
    //    }

    public function email($value)
    {
        return $this->builder->whereRaw('LOWER(email) LIKE ?', ['%'.strtolower($value).'%']);
    }

    public function name($value)
    {
        return $this->builder->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ['%'.strtolower($value).'%']);
    }

    public function search($value)
    {
        return $this->builder->where(function ($query) use ($value) {
            $query->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ['%'.strtolower($value).'%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($value).'%']);
        });
    }

    public function title($value)
    {
        return $this->builder->where('title', $value);
    }
}
