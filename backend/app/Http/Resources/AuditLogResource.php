<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AuditLog */
class AuditLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var AuditLog $auditLog */
        $auditLog = $this->resource;

        return [
            'id' => $auditLog->id,
            'tenant_id' => $auditLog->tenant_id,
            'user_id' => $auditLog->user_id,
            'event' => $auditLog->event,
            'auditable_type' => $auditLog->auditable_type,
            'auditable_id' => $auditLog->auditable_id,
            'payload' => $auditLog->payload,
            'user' => $this->whenLoaded('user', function () use ($auditLog): array {
                /** @var \App\Models\User|null $user */
                $user = $auditLog->user;

                return [
                    'id' => $user?->id,
                    'name' => $user?->name,
                    'email' => $user?->email,
                    'role' => $user?->role,
                    'status' => $user?->status,
                    'unit_id' => $user?->unit_id,
                ];
            }),
            'created_at' => $auditLog->created_at?->toIso8601String(),
            'updated_at' => $auditLog->updated_at?->toIso8601String(),
        ];
    }
}
