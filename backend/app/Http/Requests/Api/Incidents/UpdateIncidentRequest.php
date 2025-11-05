<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Incidents;

use App\Models\Incident;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use RuntimeException;

class UpdateIncidentRequest extends FormRequest
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
        $incident = $this->incident();

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:64',
                Rule::unique('incidents', 'code')
                    ->ignore($incident->getKey())
                    ->where(fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenant->getKey())),
            ],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'string', Rule::in(Incident::STATUSES)],
            'priority' => ['sometimes', 'required', 'string', Rule::in(Incident::PRIORITIES)],
            'impact_area' => ['sometimes', 'nullable', 'array'],
            'impact_area.type' => ['required_with:impact_area', 'string', Rule::in(['Polygon', 'MultiPolygon'])],
            'impact_area.coordinates' => ['required_with:impact_area', 'array'],
            'impact_area.coordinates.*' => ['array'],
            'started_at' => ['sometimes', 'nullable', 'date'],
            'closed_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:started_at'],
            'context' => ['sometimes', 'nullable', 'array'],
            'context.description' => ['nullable', 'string'],
            'context.source' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $status = $this->input('status');

            if ($status === 'closed' && ! $this->filled('closed_at')) {
                $validator->errors()->add('closed_at', 'Kapanmış olaylar için closed_at alanı gereklidir.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('code');
        $title = $this->input('title');

        if (\is_string($code)) {
            $this->merge(['code' => trim($code)]);
        }

        if (\is_string($title)) {
            $this->merge(['title' => trim($title)]);
        }

        $impactArea = $this->input('impact_area');

        if (\is_array($impactArea)) {
            $coordinates = Arr::get($impactArea, 'coordinates');

            if (! \is_array($coordinates) || $coordinates === []) {
                $this->merge(['impact_area' => null]);
            }
        }
    }

    private function incident(): Incident
    {
        $incident = $this->route('incident');

        if (! $incident instanceof Incident) {
            throw new RuntimeException('Beklenmedik olay kaydı tipi alındı.');
        }

        return $incident;
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
