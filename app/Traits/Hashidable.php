<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait Hashidable
{
    public function getRouteKey()
    {
        return static::encodeHash($this->getKey());
    }

    public function getHashidAttribute()
    {
        return static::encodeHash($this->attributes['id']);
    }

    public static function encodeHash($id): string
    {
        return static::HASHID_PREFIX.static::encodeHashRaw($id);
    }

    public static function encodeHashRaw($id): string
    {
        return Hashids::connection(get_called_class())->encode($id);
    }
}
