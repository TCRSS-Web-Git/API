<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait EloquentDecodeHash
{
    public static function decodeHash($hid)
    {
        $hash = Str::after($hid, static::HASHID_PREFIX);
        $id = \Vinkla\Hashids\Facades\Hashids::connection(get_called_class())->decode($hash);
        if ($id) {
            $id = $id[0];
        } else {
            $id = -1;
        }

        return $id;
    }
}
