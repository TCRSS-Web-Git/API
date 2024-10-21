<?php

namespace App\Models;

use App\Traits\EloquentDecodeHash;
use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use EloquentDecodeHash;
    use HasFactory;
    use Hashidable;

    public const HASHID_PREFIX = 'district_';

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function subdistricts(): HasMany
    {
        return $this->hasMany(Subdistrict::class);
    }
}
