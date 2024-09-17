<?php

namespace App\Filters;

use App\Models\User;

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

    public function id($value)
    {
        $ids = explode(',', $value);
        $ids = array_map(function ($id) {
            return User::decodeHash($id);
        }, $ids);

        return $this->builder->whereIn('id', $ids);
    }

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
        if ($value === 'null') {
            return $this->builder->whereNull('phone');
        }

        $searchPhone = str_replace('-', '', $value);
        if ($searchPhone[0] === '0') {
            $searchPhone = substr($searchPhone, 1);
        }

        return $this->builder->where('phone', 'LIKE', '%'.$searchPhone.'%');
    }

    public function search($value)
    {
        $searchPhone = str_replace('-', '', $value);
        if ($searchPhone[0] === '0') {
            $searchPhone = substr($searchPhone, 1);
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
