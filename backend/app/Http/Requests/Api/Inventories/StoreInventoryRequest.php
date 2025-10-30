<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Inventories;

use App\Models\Inventory;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use RuntimeException;

class StoreInventoryRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('inventories', 'code')->where(
                    fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(Inventory::STATUSES)],
            'last_service_at' => ['nullable', 'date', 'before_or_equal:now'],
            'attributes' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('code');
        $name = $this->input('name');

        if (\is_string($code)) {
            $this->merge(['code' => trim($code)]);
        }

        if (\is_string($name)) {
            $this->merge(['name' => trim($name)]);
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
}
