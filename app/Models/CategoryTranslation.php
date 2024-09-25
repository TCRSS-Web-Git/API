<?php

declare(strict_types=1);

namespace App\Models;

use LaravelLang\Models\Casts\TrimCast;
use LaravelLang\Models\Eloquent\Translation;
use OwenIt\Auditing\Contracts\Auditable;

class CategoryTranslation extends Translation implements Auditable
{
    use \OwenIt\Auditing\Auditable;

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
