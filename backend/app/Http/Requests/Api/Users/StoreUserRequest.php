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

class StoreUserRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'phone' => ['nullable', 'string', 'max:32'],
            'role' => ['required', 'string', 'max:64'],
            'status' => ['nullable', 'string', Rule::in(User::STATUSES)],
            'unit_id' => [
                'nullable',
                'integer',
                Rule::exists('units', 'id')->where(
                    static fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())
                ),
            ],
            'documents' => ['nullable', 'array'],
            'documents_expires_at' => ['nullable', 'date'],
            'password' => ['required', 'string', 'min:12'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->filled('documents') && ! $this->filled('documents_expires_at')) {
                $validator->errors()->add(
                    'documents_expires_at',
                    'Belgeler yüklendiğinde son geçerlilik tarihinin belirtilmesi gerekir.'
                );
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
