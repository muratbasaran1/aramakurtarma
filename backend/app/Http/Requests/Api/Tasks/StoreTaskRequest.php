<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Tasks;

use App\Models\Task;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use RuntimeException;

class StoreTaskRequest extends FormRequest
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
            'incident_id' => [
                'required',
                'integer',
                Rule::exists('incidents', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'assigned_unit_id' => [
                'nullable',
                'integer',
                Rule::exists('units', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'assigned_to' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'status' => ['nullable', 'string', Rule::in(Task::STATUSES)],
            'route' => ['nullable', 'array'],
            'route.type' => ['required_with:route', 'string', Rule::in(['LineString'])],
            'route.coordinates' => ['required_with:route', 'array', 'min:2'],
            'route.coordinates.*' => ['array', 'size:2'],
            'route.coordinates.*.*' => ['numeric'],
            'requires_double_confirmation' => ['sometimes', 'boolean'],
            'planned_start_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:planned_start_at'],
            'verified_at' => ['nullable', 'date', 'after_or_equal:completed_at'],
            'context' => ['nullable', 'array'],
            'context.notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $status = $this->input('status', 'planned');
            $completedProvided = $this->filled('completed_at');
            $verifiedProvided = $this->filled('verified_at');
            $requiresDouble = $this->has('requires_double_confirmation')
                ? filter_var(
                    $this->input('requires_double_confirmation'),
                    \FILTER_VALIDATE_BOOL,
                    \FILTER_NULL_ON_FAILURE
                ) ?? false
                : true;

            if (\in_array($status, ['done', 'verified'], true) && ! $completedProvided) {
                $validator->errors()->add('completed_at', 'Tamamlanan görevler için completed_at alanı gereklidir.');
            }

            if ($status === 'verified' && ! $verifiedProvided) {
                $validator->errors()->add('verified_at', 'Doğrulanan görevler için verified_at alanı gereklidir.');
            }

            if ($status === 'verified' && ! $requiresDouble) {
                $validator->errors()->add('requires_double_confirmation', 'Çift onay kapalıyken görev doğrulanamaz.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $route = $this->input('route');

        if (\is_array($route)) {
            $coordinates = Arr::get($route, 'coordinates');

            if (! \is_array($coordinates) || $coordinates === []) {
                $this->merge(['route' => null]);
            }
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
