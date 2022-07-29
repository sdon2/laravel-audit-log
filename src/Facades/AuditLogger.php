<?php

namespace Sdon2\AuditLog\Facades;

use Illuminate\Support\Facades\Facade;
use Sdon2\AuditLog\AuditLogger as Service;

class AuditLogger extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Service::class;
    }
}
