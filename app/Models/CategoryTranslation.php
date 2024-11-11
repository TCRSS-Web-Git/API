<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelLang\Models\Casts\TrimCast;
use LaravelLang\Models\Eloquent\Translation;
use OwenIt\Auditing\Contracts\Auditable;

class CategoryTranslation extends Translation implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $touches = ['parent'];

    protected $fillable = [
        'locale',
        'name',
        'description',
    ];

    protected $casts = [
        'name' => TrimCast::class,
        'description' => TrimCast::class,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'item_id');
    }
}
