<?php

namespace App\Models;

use App\Traits\EloquentDecodeHash;
use App\Traits\EloquentFindByHash;
use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role as Model;

class Role extends Model implements Auditable
{
    use EloquentDecodeHash;
    use EloquentFindByHash;
    use HasFactory;
    use Hashidable;
    use \OwenIt\Auditing\Auditable;

    public const HASHID_PREFIX = 'role_';

    // Default roles, can be changed as needed by user
    public const ROLE_SUPER_ADMIN = 'Super Admin';

    public const ROLE_ADMIN = 'Admin';
}
