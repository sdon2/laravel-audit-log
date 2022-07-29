<?php

namespace Sdon2\AuditLog\Traits;

use Sdon2\AuditLog\AuditLogServiceProvider;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait CausesAudit
{
    public function activity(): MorphMany
    {
        return $this->morphMany(AuditLogServiceProvider::determineAuditLogModel(), 'causer');
    }

    /** @deprecated Use activity() instead */
    public function loggedActivity(): MorphMany
    {
        return $this->activity();
    }
}
