<?php

namespace App\Filters;

use App\Models\Role;
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

    public function id($value)
    {
        $ids = explode(',', $value);
        $ids = array_map(static function ($id) {
            return User::decodeHash(trim($id));
        }, $ids);
        $ids = array_filter($ids, static function ($id) {
            return $id > 0;
        });

        return $this->builder->whereIn('id', $ids);
    }

    public function email($value)
    {
        return $this->builder->whereRaw('LOWER(email) LIKE ?', ['%'.strtolower($value).'%']);
    }

    public function name($value)
    {
        $expression = config('database.default') === 'sqlite'
                ? "LOWER(first_name || ' ' || last_name) LIKE ?"
                : "LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?";

        return $this->builder->whereRaw($expression, ['%'.strtolower($value).'%']);
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

    public function role_id($value)
    {
        $roleIds = explode(',', $value);
        $roleIds = array_map(static function ($id) {
            return Role::decodeHash(trim($id));
        }, $roleIds);
        $roleIds = array_filter($roleIds, static function ($id) {
            return $id > 0;
        });

        if (empty($roleIds)) {
            return $this->builder;
        }

        return $this->builder->whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('id', $roleIds);
        });
    }

    public function search($value)
    {
        // Search phone number
        $searchPhone = str_replace('-', '', $value);
        if ($searchPhone[0] === '0') {
            $searchPhone = substr($searchPhone, 1);
        }

        // Search by hashID (support multiple hashID, comma separated)
        $ids = User::decodeMultipleHashString($value);

        $nameExpression = config('database.default') === 'sqlite'
            ? "LOWER(first_name || ' ' || last_name) LIKE ?"
            : "LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?";

        return $this->builder->where(function ($query) use ($value, $searchPhone, $ids, $nameExpression) {
            $query->whereRaw($nameExpression, ['%'.strtolower($value).'%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($value).'%'])
                ->orWhere('phone', 'LIKE', '%'.$searchPhone.'%')
                ->orWhereIn('id', $ids);
        });
    }

    public function title($value)
    {
        return $this->builder->where('title', $value);
    }
}
