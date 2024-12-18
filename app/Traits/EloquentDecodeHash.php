<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait EloquentDecodeHash
{
    public static function decodeHash($hid): int
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

    /**
     * @return array<int>
     */
    public static function decodeMultipleHashString(string $hids): array
    {
        $hashIds = explode(',', $hids);
        $ids = array_map(static function ($hashId) {
            return static::decodeHash(trim($hashId));
        }, $hashIds);

        return array_filter($ids, static function ($id) {
            return $id > 0;
        });
    }
}
