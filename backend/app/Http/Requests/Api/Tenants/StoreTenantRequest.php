<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Tenants;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTenantRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tenants', 'slug'),
            ],
            'timezone' => ['required', 'string', 'timezone:all'],
            'settings' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->filled('settings') && ! \is_array($this->input('settings'))) {
                $validator->errors()->add('settings', 'Settings alanı bir dizi olmalıdır.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $name = $this->stringInput('name');

        if ($name !== null) {
            $this->merge(['name' => $name]);
        }

        $slug = $this->stringInput('slug');

        if ($slug === null && $name !== null) {
            $slug = $name;
        }

        if ($slug !== null) {
            $slug = Str::slug($slug);
        }

        if ($slug !== null && $slug !== '') {
            $this->merge(['slug' => $slug]);
        }

        $timezone = $this->stringInput('timezone') ?? 'Europe/Istanbul';
        $this->merge(['timezone' => $timezone]);

        $settings = $this->input('settings');

        if (! \is_array($settings) || $settings === []) {
            $this->merge(['settings' => null]);
        }
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
