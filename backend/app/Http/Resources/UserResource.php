<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User $user */
        $user = $this->resource;
        $unitRelation = null;

        if (method_exists($user, 'relationLoaded') && $user->relationLoaded('unit')) {
            $unitRelation = $user->getRelationValue('unit');
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'status' => $user->status,
            'documents' => $user->documents,
            'documents_expires_at' => $user->documents_expires_at?->toIso8601String(),
            'unit_id' => $user->unit_id,
            'unit' => $this->when(
                $unitRelation !== null,
                function () use ($unitRelation): array {
                    return [
                        'id' => $unitRelation?->id,
                        'name' => $unitRelation?->name,
                    ];
                }
            ),
            'created_at' => $user->created_at?->toIso8601String(),
            'updated_at' => $user->updated_at?->toIso8601String(),
        ];
    }
}
