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
use Illuminate\Validation\Validator;
use RuntimeException;

class StoreUnitRequest extends FormRequest
{
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

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('units', 'slug')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'type' => ['required', 'string', Rule::in(Unit::TYPES)],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('slug')) {
                $validator->errors()->add('slug', 'Slug değeri boş bırakılamaz.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $name = $this->stringInput('name');

        if ($name !== null) {
            $this->merge(['name' => $name]);
        }

        $slugInput = $this->input('slug');
        $slug = null;

        if (\is_string($slugInput)) {
            $slug = Str::slug($slugInput);
        } elseif ($slugInput === null && $name !== null) {
            $slug = Str::slug($name);
        }

        if ($slug !== null && $slug !== '') {
            $this->merge(['slug' => $slug]);
        }

        $type = $this->stringInput('type');

        if ($type !== null) {
            $this->merge(['type' => $type]);
        }

        $metadata = $this->input('metadata');

        if (! \is_array($metadata) || $metadata === []) {
            $this->merge(['metadata' => null]);
        }
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
}
