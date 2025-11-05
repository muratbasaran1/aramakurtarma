<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $tenant_id
 * @property int $user_id
 * @property int|null $task_id
 * @property float $latitude
 * @property float $longitude
 * @property float|null $speed
 * @property float|null $heading
 * @property float|null $accuracy
 * @property array|null $context
 * @property Carbon|null $captured_at
 * @property-read Task|null $task
 * @property-read User|null $user
 */
class TrackingPing extends Model
{
    /** @use HasFactory<\Database\Factories\TrackingPingFactory> */
    use HasFactory;
    use BelongsToTenant;

    protected $table = 'tracking_pings';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'task_id',
        'latitude',
        'longitude',
        'speed',
        'heading',
        'accuracy',
        'captured_at',
        'context',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'context' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'speed' => 'float',
        'heading' => 'float',
        'accuracy' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function wasStationaryComparedTo(self $other, float $distanceThreshold = 5.0, float $speedThreshold = 0.3): bool
    {
        if ($this->speed !== null && $this->speed > $speedThreshold) {
            return false;
        }

        if ($other->speed !== null && $other->speed > $speedThreshold) {
            return false;
        }

        return $this->distanceTo($other) <= $distanceThreshold;
    }

    public function distanceTo(self $other): float
    {
        $earthRadius = 6371000.0;

        $latFrom = deg2rad($this->latitude);
        $latTo = deg2rad($other->latitude);
        $latDelta = deg2rad($other->latitude - $this->latitude);
        $lonDelta = deg2rad($other->longitude - $this->longitude);

        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
