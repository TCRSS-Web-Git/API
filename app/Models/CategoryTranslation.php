<?php

declare(strict_types=1);

namespace App\Models;

use LaravelLang\Models\Casts\TrimCast;
use LaravelLang\Models\Eloquent\Translation;

class CategoryTranslation extends Translation
{
    protected $fillable = [
        'locale',
        'name',
        'description',
    ];

    protected $casts = [
        'name' => TrimCast::class,
        'description' => TrimCast::class,
    ];
}
