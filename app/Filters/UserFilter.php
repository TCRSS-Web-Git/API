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

    public function first_name($value)
    {
        return $this->builder->whereRaw('LOWER(first_name) LIKE ?', ['%'.strtolower($value).'%']);
    }

    public function last_name($value)
    {
        return $this->builder->whereRaw('LOWER(last_name) LIKE ?', ['%'.strtolower($value).'%']);
    }

    public function phone($value)
    {
        $searchPhone = $value;
        if (substr($value, 0, 1) === '0') {
            $searchPhone = '+66'.substr($value, 1);
        }

        return $this->builder->where('phone', 'LIKE', '%'.$searchPhone.'%');
    }

    public function search($value)
    {
        $searchPhone = $value;
        if (substr($value, 0, 1) === '0') {
            $searchPhone = '+66'.substr($value, 1);
        }

        return $this->builder->where(function ($query) use ($value, $searchPhone) {
            $query->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ['%'.strtolower($value).'%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($value).'%'])
                ->orWhere('phone', 'LIKE', '%'.$searchPhone.'%');
        });
    }

    public function title($value)
    {
        return $this->builder->where('title', $value);
    }
}
