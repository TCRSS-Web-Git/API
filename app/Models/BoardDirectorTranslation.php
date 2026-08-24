<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelLang\Models\Casts\TrimCast;
use LaravelLang\Models\Eloquent\Translation;
use OwenIt\Auditing\Contracts\Auditable;

class BoardDirectorTranslation extends Translation implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $touches = ['parent'];

    protected $fillable = [
        'locale',
        'name',
        'position',
    ];

    protected $casts = [
        'name' => TrimCast::class,
        'position' => TrimCast::class,
    ];

    public function search($query)
    {
        return self::whereRaw('MATCH(name, position) AGAINST(? IN BOOLEAN MODE)', [$query]);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BoardDirector::class, 'item_id');
    }
}
