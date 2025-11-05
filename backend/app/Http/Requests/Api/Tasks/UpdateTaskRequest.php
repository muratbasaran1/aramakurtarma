<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Tasks;

use App\Models\Task;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use RuntimeException;

class UpdateTaskRequest extends FormRequest
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
                'sometimes',
                'required',
                'integer',
                Rule::exists('incidents', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'assigned_unit_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('units', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'assigned_to' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'status' => ['sometimes', 'required', 'string', Rule::in(Task::STATUSES)],
            'route' => ['sometimes', 'nullable', 'array'],
            'route.type' => ['required_with:route', 'string', Rule::in(['LineString'])],
            'route.coordinates' => ['required_with:route', 'array', 'min:2'],
            'route.coordinates.*' => ['array', 'size:2'],
            'route.coordinates.*.*' => ['numeric'],
            'requires_double_confirmation' => ['sometimes', 'boolean'],
            'planned_start_at' => ['sometimes', 'nullable', 'date'],
            'completed_at' => ['sometimes', 'nullable', 'date'],
            'verified_at' => ['sometimes', 'nullable', 'date'],
            'context' => ['sometimes', 'nullable', 'array'],
            'context.notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $task = $this->task();

            $status = $this->input('status');
            $finalStatus = \is_string($status) ? $status : $task->status;

            $completedAtInput = $this->input('completed_at');
            $verifiedAtInput = $this->input('verified_at');
            $plannedStartInput = $this->input('planned_start_at');

            $completedAt = \is_string($completedAtInput)
                ? Carbon::make($completedAtInput)
                : $task->completed_at;

            $verifiedAt = \is_string($verifiedAtInput)
                ? Carbon::make($verifiedAtInput)
                : $task->verified_at;

            $plannedStartAt = \is_string($plannedStartInput)
                ? Carbon::make($plannedStartInput)
                : $task->planned_start_at;

            $requiresDouble = $this->has('requires_double_confirmation')
                ? filter_var(
                    $this->input('requires_double_confirmation'),
                    \FILTER_VALIDATE_BOOL,
                    \FILTER_NULL_ON_FAILURE
                ) ?? false
                : (bool) $task->requires_double_confirmation;

            if ($plannedStartAt !== null && $completedAt !== null && $completedAt->lessThan($plannedStartAt)) {
                $validator->errors()->add('completed_at', 'Tamamlanma tarihi planlanan başlangıçtan önce olamaz.');
            }

            if ($completedAt !== null && $verifiedAt !== null && $verifiedAt->lessThan($completedAt)) {
                $validator->errors()->add('verified_at', 'Doğrulama tarihi tamamlanma tarihinden önce olamaz.');
            }

            if (\in_array($finalStatus, [Task::STATUS_DONE, Task::STATUS_VERIFIED], true) && $completedAt === null) {
                $validator->errors()->add('completed_at', 'Tamamlanan görevler için completed_at alanı gereklidir.');
            }

            if ($finalStatus === Task::STATUS_VERIFIED && $verifiedAt === null) {
                $validator->errors()->add('verified_at', 'Doğrulanan görevler için verified_at alanı gereklidir.');
            }

            if ($finalStatus === Task::STATUS_VERIFIED && ! $requiresDouble) {
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

    private function task(): Task
    {
        $task = $this->route('task');

        if (! $task instanceof Task) {
            throw new RuntimeException('Beklenmedik görev kaydı tipi alındı.');
        }

        return $task;
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
