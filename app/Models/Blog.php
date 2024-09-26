<?php

namespace App\Models;

use App\Filters\QueryFilter;
use App\Traits\EloquentDecodeHash;
use App\Traits\EloquentFindByHash;
use App\Traits\Hashidable;
use App\Traits\LatestAudit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelLang\Models\HasTranslations;
use OwenIt\Auditing\Contracts\Auditable;

class Blog extends Model implements Auditable
{
    use EloquentDecodeHash;
    use EloquentFindByHash;
    use HasFactory;
    use Hashidable;
    use HasTranslations;
    use LatestAudit;
    use \OwenIt\Auditing\Auditable;

    public const HASHID_PREFIX = 'blog_';

    protected $fillable = [
        'slug',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getTranslationTable()
    {
        return 'blog_translations';
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
