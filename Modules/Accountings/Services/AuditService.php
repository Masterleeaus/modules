<?php

namespace Modules\Accountings\Services;

use Modules\Accountings\Entities\AuditLog;

class AuditService
{
    public static function log(string $action, string $entityType, ?int $entityId = null, array $meta = []): void
    {
        // BaseModel + HasCompany/HasUserScope will fill company_id/user_id via global scopes where applicable.
        AuditLog::create([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'meta' => empty($meta) ? null : $meta,
            'created_at' => now(),
        ]);
    }
}
