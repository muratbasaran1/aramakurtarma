<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Tenants;

use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use RuntimeException;

class UpdateTenantRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('tenants', 'slug')->ignore($tenant->getKey()),
            ],
            'timezone' => ['sometimes', 'string', 'timezone:all'],
            'settings' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->has('slug') && $this->stringInput('slug') === null) {
                $validator->errors()->add('slug', 'Slug değeri boş bırakılamaz.');
            }

            if ($this->filled('settings') && ! \is_array($this->input('settings'))) {
                $validator->errors()->add('settings', 'Settings alanı bir dizi olmalıdır.');
            }
        });
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
            $slug = $this->stringInput('slug');

            if ($slug !== null) {
                $this->merge(['slug' => Str::slug($slug)]);
            }
        }

        if ($this->has('timezone')) {
            $timezone = $this->stringInput('timezone');

            if ($timezone !== null) {
                $this->merge(['timezone' => $timezone]);
            }
        }

        $settings = $this->input('settings');

        if ($this->has('settings') && (! \is_array($settings) || $settings === [])) {
            $this->merge(['settings' => null]);
        }
    }

    public function resolvedTenant(): Tenant
    {
        return $this->tenant();
    }

    private function tenant(): Tenant
    {
        $tenant = $this->route('tenant');

        if (! $tenant instanceof Tenant) {
            throw new RuntimeException('Tenant kaydı bulunamadı.');
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
