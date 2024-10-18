<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelLang\Models\Casts\TrimCast;
use LaravelLang\Models\Eloquent\Translation;
use OwenIt\Auditing\Contracts\Auditable;

class AnnualReportTranslation extends Translation implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $touches = ['parent'];

    protected $fillable = [
        'locale',
        'title',
    ];

    protected $casts = [
        'title' => TrimCast::class,
    ];

    public function search($query)
    {
        return self::whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$query]);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AnnualReport::class, 'item_id');
    }
}
