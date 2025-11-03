<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Units;

use App\Models\Tenant;
use App\Models\Unit;
use App\Tenant\TenantContext;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

use function abort;

class UpdateUnitRequest extends FormRequest
{
    private ?Unit $resolvedUnit = null;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tenant = $this->tenant();
        $unitId = $this->unitId();

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'slug')
                    ->ignore($unitId)
                    ->where(static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())),
            ],
            'type' => ['sometimes', 'required', 'string', Rule::in(Unit::TYPES)],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $name = $this->stringInput('name');

            if ($name !== null) {
                $this->merge(['name' => $name]);
            }
        }

        if ($this->has('slug')) {
            $slugInput = $this->input('slug');
            $slug = \is_string($slugInput) ? Str::slug($slugInput) : null;

            $this->merge(['slug' => $slug !== '' ? $slug : null]);
        }

        if ($this->has('type')) {
            $type = $this->stringInput('type');

            if ($type !== null) {
                $this->merge(['type' => $type]);
            }
        }

        if ($this->has('metadata')) {
            $metadata = $this->input('metadata');

            if (! \is_array($metadata) || $metadata === []) {
                $this->merge(['metadata' => null]);
            }
        }
    }

    public function resolvedUnit(): Unit
    {
        if ($this->resolvedUnit instanceof Unit) {
            return $this->resolvedUnit;
        }

        $tenant = $this->tenant();
        $identifier = $this->route('unit');

        $unit = $identifier instanceof Unit
            ? $identifier
            : Unit::findForTenantByIdentifier($tenant, $this->normalizeIdentifier($identifier));

        if ($unit === null) {
            abort(404);
        }

        if (! $unit instanceof Unit) {
            throw new RuntimeException('Beklenmedik birim modeli alındı.');
        }

        $this->resolvedUnit = $unit;

        return $unit;
    }

    private function unitId(): int
    {
        return (int) $this->resolvedUnit()->getKey();
    }

    private function tenant(): Tenant
    {
        $tenant = app(TenantContext::class)->tenant();

        if ($tenant === null) {
            throw new RuntimeException('Aktif tenant bağlamı bulunamadı.');
        }

        return $tenant;
    }

    private function stringInput(string $key): ?string
    {
        $value = $this->input($key);

        if (! \is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeIdentifier(mixed $identifier): int|string
    {
        if (\is_int($identifier) || \is_string($identifier)) {
            return $identifier;
        }

        if (is_numeric($identifier)) {
            return (int) $identifier;
        }

        return (string) $identifier;
    }
}
