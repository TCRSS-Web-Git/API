<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelLang\Models\Casts\TrimCast;
use LaravelLang\Models\Eloquent\Translation;
use OwenIt\Auditing\Contracts\Auditable;

class JobPostTranslation extends Translation implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $touches = ['parent'];

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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(JobPost::class, 'item_id');
    }
}
