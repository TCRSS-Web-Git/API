<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait LatestAudit
{
    /**
     * Auditable Model audits.
     *
     * @return MorphOne<Audit>
     */
    public function latestAudit(): MorphOne
    {
        return $this->morphOne(Audit::class, 'auditable')->latest()->limit(1);
    }
}
