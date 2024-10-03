<?php

namespace App\Models;

use App\Enums\CategoryType;
use App\Traits\EloquentDecodeHash;
use App\Traits\EloquentFindByHash;
use App\Traits\Hashidable;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Category extends Model implements Auditable
{
    use EloquentDecodeHash;
    use EloquentFindByHash;
    use HasFactory;
    use Hashidable;
    use HasTranslations;
    use \OwenIt\Auditing\Auditable;

    public const HASHID_PREFIX = 'category_';

    protected $fillable = [
        'type',
        'slug',
        'sort',
    ];

    protected $casts = [
        'type' => CategoryType::class,
    ];

    public function getTranslationTable()
    {
        return 'category_translations';
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function scopeBlog($query)
    {
        return $query->where('type', CategoryType::BLOG);
    }
}
