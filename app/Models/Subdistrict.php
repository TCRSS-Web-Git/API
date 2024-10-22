<?php

namespace App\Models;

use App\Traits\EloquentDecodeHash;
use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subdistrict extends Model
{
    use EloquentDecodeHash;
    use HasFactory;
    use Hashidable;

    public const HASHID_PREFIX = 'subdistrict_';

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
