<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int $incident_id
 * @property int|null $assigned_unit_id
 * @property int|null $assigned_to
 * @property string $status
 * @property array|string|null $route
 * @property bool $requires_double_confirmation
 * @property Carbon|null $planned_start_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $verified_at
 * @property array|null $context
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|self forTenant(Tenant|int|string $tenant)
 * @method Builder forTenant(Tenant|int|string $tenant)
 */
class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use BelongsToTenant;

    /**
     * @var list<string>
     */
    public const STATUSES = ['planned', 'assigned', 'in_progress', 'done', 'verified'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'incident_id',
        'assigned_unit_id',
        'assigned_to',
        'status',
        'route',
        'requires_double_confirmation',
        'planned_start_at',
        'completed_at',
        'verified_at',
        'context',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'route' => 'array',
            'requires_double_confirmation' => 'boolean',
            'planned_start_at' => 'datetime',
            'completed_at' => 'datetime',
            'verified_at' => 'datetime',
            'context' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function assignedUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'assigned_unit_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
