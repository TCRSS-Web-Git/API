<?php

declare(strict_types=1);

namespace App\Models;

use LaravelLang\Models\Casts\TrimCast;
use LaravelLang\Models\Eloquent\Translation;

class BlogTranslation extends Translation
{
    protected $fillable = [
        'locale',
        'title',
        'body',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'title' => TrimCast::class,
        'body' => TrimCast::class,
        'meta_title' => TrimCast::class,
        'meta_description' => TrimCast::class,
    ];

    public function search($query)
    {
        return self::whereRaw('MATCH(title, body) AGAINST(? IN BOOLEAN MODE)', [$query]);
    }
}
