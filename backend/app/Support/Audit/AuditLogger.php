<?php

declare(strict_types=1);

namespace App\Support\Audit;

use App\Models\AuditLog;
use App\Tenant\TenantContext;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * @param list<string> $sensitiveKeys
     */
    public function __construct(
        private readonly TenantContext $tenantContext,
        private readonly AuthFactory $auth,
        private readonly array $sensitiveKeys = ['password', 'password_confirmation', 'token']
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function record(string $event, Model $model, array $context = []): void
    {
        $tenantId = $context['tenant_id'] ?? $this->tenantContext->tenantId();

        if ($tenantId === null) {
            $tenantAttribute = $model->getAttribute('tenant_id');

            if ($tenantAttribute !== null) {
                $tenantId = (int) $tenantAttribute;
            }
        }

        $payload = $this->buildPayload($model, $context);

        $morphClass = method_exists($model, 'getMorphClass')
            ? $model->getMorphClass()
            : $model::class;

        AuditLog::query()->create([
            'tenant_id' => $tenantId,
            'user_id' => $context['user_id'] ?? $this->auth->guard()->id(),
            'event' => $event,
            'auditable_type' => $morphClass,
            'auditable_id' => (int) $model->getKey(),
            'payload' => $payload,
        ]);
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    private function buildPayload(Model $model, array $context): array
    {
        $payload = [];

        /** @psalm-suppress UndefinedMethod */
        $attributes = $context['attributes'] ?? $model->toArray();

        if ($attributes !== []) {
            $payload['attributes'] = $attributes;
        }

        $changes = $context['changes'] ?? [];

        if ($changes !== []) {
            $payload['changes'] = $this->sanitize($changes);
        }

        if (isset($context['meta'])) {
            $payload['meta'] = $context['meta'];
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (\in_array((string) $key, $this->sensitiveKeys, true)) {
                $data[$key] = '***';

                continue;
            }

            if (\is_array($value)) {
                /** @var array<string, mixed> $nested */
                $nested = $value;
                $data[$key] = $this->sanitize($nested);
            }
        }

        return $data;
    }
}
