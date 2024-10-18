<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelLang\Models\Casts\TrimCast;
use LaravelLang\Models\Eloquent\Translation;
use OwenIt\Auditing\Contracts\Auditable;

class AwardTranslation extends Translation implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $touches = ['parent'];

    protected $fillable = [
        'locale',
        'title',
        'description',
    ];

    protected $casts = [
        'title' => TrimCast::class,
        'body' => TrimCast::class,
    ];

    public function search($query)
    {
        return self::whereRaw('MATCH(title, description) AGAINST(? IN BOOLEAN MODE)', [$query]);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Award::class, 'item_id');
    }
}
