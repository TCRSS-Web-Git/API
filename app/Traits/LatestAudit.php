<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait LatestAudit
{
    /**
     * Auditable Model audits.
     *
     * @return MorphOne<Audit, covariant Model>
     */
    public function latestAudit(): MorphOne
    {
        return $this->morphOne(Audit::class, 'auditable')->latest()->limit(1);
    }

    public function firstAudit(): MorphOne
    {
        return $this->morphOne(Audit::class, 'auditable')->oldest()->limit(1);
    }
}
