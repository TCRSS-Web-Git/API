<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait EloquentFindByHash
{
    //    /**
    //     * @param string $hashid
    //     * @return \Illuminate\Database\Eloquent\Model
    //     */
    //    public static function findByHashOrFailWithTrashed(string $hashid)
    //    {
    //        try {
    //            $id = static::decodeHash($hashid);
    //        } catch (\Exception $exception) {
    //            throw new NotFoundHttpException();
    //        }
    //        return static::withTrashed()->findOrFail($id);
    //    }

    public static function findByHashOrFail(string $hashid): Model
    {
        try {
            $id = static::decodeHash($hashid);
        } catch (\Exception $exception) {
            throw new NotFoundHttpException;
        }

        return static::findOrFail($id);
    }

    public static function findByHash(string $hashid): Model|static|null
    {
        try {
            $id = static::decodeHash($hashid);
        } catch (\Exception $exception) {
            return null;
        }

        return static::find($id);
    }
}
