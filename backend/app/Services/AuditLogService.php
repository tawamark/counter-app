<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogService
{
    public function record(User $user, string $module, string $action, string $description, ?Model $auditable = null, array $metadata = []): AuditLog
    {
        $request = request();

        return AuditLog::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'module' => $module,
            'action' => $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'description' => $description,
            'metadata' => $metadata === [] ? null : $metadata,
            'ip_address' => $request instanceof Request ? $request->ip() : null,
            'user_agent' => $request instanceof Request ? mb_substr((string) $request->userAgent(), 0, 500) : null,
        ]);
    }
}
