<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tenant_id
 * @property string $code
 * @property string $title
 * @property string $status
 * @property string $priority
 * @property array|null $impact_area
 * @property Carbon|null $started_at
 * @property Carbon|null $closed_at
 * @property array|null $context
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder forTenant(Tenant|int|string $tenant)
 * @method static Builder forTenantQuery(Tenant|int|string $tenant)
 * @method Builder forTenant(Tenant|int|string $tenant)
 */
class Incident extends Model
{
    /** @use HasFactory<\Database\Factories\IncidentFactory> */
    use HasFactory;
    use BelongsToTenant;

    public const STATUS_OPEN = 'open';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    /**
     * @var list<string>
     */
    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_ACTIVE,
        self::STATUS_CLOSED,
    ];

    /**
     * @var list<string>
     */
    public const PRIORITIES = ['low', 'medium', 'high', 'critical'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'code',
        'title',
        'status',
        'priority',
        'impact_area',
        'started_at',
        'closed_at',
        'context',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'impact_area' => 'array',
            'started_at' => 'datetime',
            'closed_at' => 'datetime',
            'context' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
