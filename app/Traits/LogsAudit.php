<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait LogsAudit
{
    protected function logAudit(string $action, Model $model, ?array $oldValues, ?array $newValues, ?Request $request = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => $request?->user()?->id,
            'action' => $action,
            'model_type' => $model::class,
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $request ? [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ] : null,
        ]);
    }
}
