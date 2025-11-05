<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Tracking;

use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Tenant\TenantContext;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use RuntimeException;

class StoreTrackingPingRequest extends FormRequest
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
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'task_id' => [
                'nullable',
                'integer',
                Rule::exists('tasks', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'speed' => ['nullable', 'numeric', 'min:0'],
            'heading' => ['nullable', 'numeric', 'min:0', 'max:360'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'captured_at' => ['nullable', 'date'],
            'context' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated(mixed $key = null, mixed $default = null): array
    {
        $validated = parent::validated($key, $default);

        if (! isset($validated['captured_at'])) {
            $validated['captured_at'] = now();
        }

        return $validated;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $taskId = $this->input('task_id');

            if ($taskId === null) {
                return;
            }

            $tenant = $this->tenant();

            /** @var Task|null $task */
            $task = Task::query()
                ->whereKey($taskId)
                ->where('tenant_id', $tenant->getKey())
                ->first();

            if (! $task instanceof Task) {
                return;
            }

            $userId = $this->input('user_id');

            /** @var User|null $user */
            $user = User::query()
                ->whereKey($userId)
                ->where('tenant_id', $tenant->getKey())
                ->first();

            if (! $user instanceof User) {
                return;
            }

            if (! \in_array($task->status, [Task::STATUS_ASSIGNED, Task::STATUS_IN_PROGRESS], true)) {
                $validator->errors()->add('task_id', 'Görev aktif durumda değilken konum ping kaydedilemez.');

                return;
            }

            if ($task->assigned_to !== null && (int) $task->assigned_to !== (int) $user->getKey()) {
                $validator->errors()->add('task_id', 'Görev bu kullanıcıya atanmadı.');
            }

            if (
                $task->assigned_to === null
                && $task->assigned_unit_id !== null
                && ($user->unit_id === null || (int) $task->assigned_unit_id !== (int) $user->unit_id)
            ) {
                $validator->errors()->add('task_id', 'Görev atanmış birim ile kullanıcının birimi uyuşmuyor.');
            }
        });
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
