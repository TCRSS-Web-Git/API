<?php

namespace App\Models;

use App\Traits\EloquentDecodeHash;
use App\Traits\EloquentFindByHash;
use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission as Model;

class Permission extends Model implements Auditable
{
    use EloquentDecodeHash;
    use EloquentFindByHash;
    use HasFactory;
    use Hashidable;
    use \OwenIt\Auditing\Auditable;

    public const HASHID_PREFIX = 'permission_';
}
