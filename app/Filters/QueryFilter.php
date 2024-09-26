<?php

namespace App\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $builder;

    protected $request;

    protected $sortable = []; // Add sortable fields here

    protected $defaultSort = ['column' => 'id', 'direction' => 'desc']; // Edit default sorting here

    protected $translatedFields = []; // Add translated fields here

    protected $joinedTables = []; // Internal DO NOT EDIT, Keep track of joined tables

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (trim($value) === '') {
                continue;
            }

            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        // Apply default sorting if no sorting is specified
        if (! $this->request->has('sort')) {
            $this->builder->orderBy($this->defaultSort['column'], $this->defaultSort['direction']);
        }

        return $this->builder;
    }

    protected function include($value)
    {
        $includes = explode(',', $value);

        if (in_array('translations', $includes)) {
            $this->builder->with('translations');
        }
    }

    protected function filter($arr)
    {
        foreach ($arr as $key => $value) {
            if (trim($value) === '') {
                continue;
            }

            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    protected function sort($value)
    {
        $sortAttributes = explode(',', $value);
        $currentLocale = app()->getLocale(); // Get the current locale
        $fallbackLocale = $currentLocale === 'th' ? 'en' : 'th'; // Set the fallback locale

        foreach ($sortAttributes as $sortAttribute) {
            $sortDirection = 'asc';

            if (substr($sortAttribute, 0, 1) === '-') {
                $sortDirection = 'desc';
                $sortAttribute = substr($sortAttribute, 1);
            }

            if (! in_array($sortAttribute, $this->sortable) && ! array_key_exists($sortAttribute, $this->sortable)) {
                continue;
            }

            if (in_array($sortAttribute, $this->translatedFields)) {
                $translationTable = $this->builder->getModel()->getTranslationTable();
                if (! in_array($translationTable, $this->joinedTables)) {
                    $this->builder->select($this->builder->getModel()->getTable().'.*')
                        ->leftJoin($translationTable.' as current_locale', function ($join) use ($currentLocale) {
                            $join->on($this->builder->getModel()->getTable().'.id', '=', 'current_locale.item_id')
                                ->where('current_locale.locale', '=', $currentLocale);
                        })
                        ->leftJoin($translationTable.' as fallback_locale', function ($join) use ($fallbackLocale) {
                            $join->on($this->builder->getModel()->getTable().'.id', '=', 'fallback_locale.item_id')
                                ->where('fallback_locale.locale', '=', $fallbackLocale);
                        });
                    $this->joinedTables[] = $translationTable; // Mark the table as joined
                }
                $this->builder->orderByRaw("COALESCE(current_locale.$sortAttribute, fallback_locale.$sortAttribute) $sortDirection");
            } else {
                $columnName = $this->sortable[$sortAttribute] ?? $sortAttribute;
                $this->builder->orderBy($columnName, $sortDirection);
            }
        }
    }

    public function created_at($value)
    {
        $dates = explode(',', $value);
        // convert from Asia/Bangkok to UTC
        $dates = array_map(function ($date) {
            return Carbon::parse($date, 'Asia/Bangkok')->tz('UTC');
        }, $dates);

        if (count($dates) == 2) {
            return $this->builder->whereBetween('created_at', $dates);
        }

        // TODO change to manual where between
        return $this->builder->whereDate('created_at', $dates[0]);
    }

    public function updated_at($value)
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->whereDate('updated_at', $value);
    }
}
