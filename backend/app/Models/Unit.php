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
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property array|null $metadata
 * @property int|null $users_count
 * @property int|null $tasks_count
 * @property int|null $active_tasks_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|self forTenant(Tenant|int|string $tenant)
 * @method Builder forTenant(Tenant|int|string $tenant)
 */
class Unit extends Model
{
    /** @use HasFactory<\Database\Factories\UnitFactory> */
    use HasFactory;
    use BelongsToTenant;

    /**
     * @var list<string>
     */
    public const TYPES = ['command', 'logistics', 'medical', 'search-and-rescue'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'type',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_unit_id');
    }
}
