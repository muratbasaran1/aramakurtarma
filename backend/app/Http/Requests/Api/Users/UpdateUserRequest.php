<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Users;

use App\Models\Tenant;
use App\Models\User;
use App\Tenant\TenantContext;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use RuntimeException;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->resolveUserKey();

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($userId)
                    ->where(static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())),
            ],
            'phone' => ['sometimes', 'nullable', 'string', 'max:32'],
            'role' => ['sometimes', 'string', 'max:64'],
            'status' => ['sometimes', 'string', Rule::in(User::STATUSES)],
            'unit_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('units', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'documents' => ['sometimes', 'nullable', 'array'],
            'documents_expires_at' => ['sometimes', 'nullable', 'date'],
            'password' => ['sometimes', 'string', 'min:12'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->has('documents')) {
                $documents = $this->input('documents');

                if ($documents !== null && ! \is_array($documents)) {
                    $validator->errors()->add('documents', 'Belgeler nesne formatında olmalıdır.');
                }

                if ($documents && ! $this->filled('documents_expires_at')) {
                    $validator->errors()->add(
                        'documents_expires_at',
                        'Belgeler güncellendiğinde son geçerlilik tarihinin belirtilmesi gerekir.'
                    );
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge(array_filter([
            'name' => $this->stringInput('name'),
            'email' => $this->stringInput('email'),
            'phone' => $this->stringInput('phone'),
            'role' => $this->stringInput('role'),
        ], static fn ($value) => $value !== null));

        if ($this->has('documents')) {
            $documents = $this->input('documents');

            if (\is_array($documents) && Arr::isAssoc($documents) && $documents !== []) {
                return;
            }

            if ($documents === null || $documents === [] || $documents === '') {
                $this->merge([
                    'documents' => null,
                ]);
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

    private function resolveUserKey(): ?int
    {
        $user = $this->route('user');

        if ($user instanceof User) {
            $key = $user->getKey();

            return is_numeric($key) ? (int) $key : null;
        }

        if (is_numeric($user)) {
            return (int) $user;
        }

        return null;
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
