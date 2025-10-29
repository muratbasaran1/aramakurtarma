<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait InterpretsFilters
{
    /**
     * @param list<string> $allowed
     * @return list<string>
     *
     * @throws ValidationException
     */
    private function extractListFilter(Request $request, string $key, array $allowed): array
    {
        $raw = $request->query($key);

        if ($raw === null) {
            return [];
        }

        $values = \is_array($raw)
            ? array_map(static fn (mixed $value): string => trim((string) $value), $raw)
            : array_map(static fn (string $value): string => trim($value), explode(',', (string) $raw));

        $values = array_values(array_filter(
            $values,
            static fn (string $value): bool => $value !== ''
        ));

        if ($values === []) {
            return [];
        }

        $invalid = array_values(array_diff($values, $allowed));

        if ($invalid !== []) {
            throw ValidationException::withMessages([
                $key => \sprintf(
                    '%s değeri geçersiz: %s. İzin verilen değerler: %s',
                    ucfirst(str_replace('_', ' ', $key)),
                    implode(', ', $invalid),
                    implode(', ', $allowed)
                ),
            ]);
        }

        return array_values(array_unique($values));
    }

    /**
     * @return positive-int
     *
     * @throws ValidationException
     */
    private function extractPerPage(Request $request): int
    {
        $raw = $request->query('per_page');

        if ($raw === null || $raw === '') {
            $perPage = 15;
        } elseif (is_numeric($raw)) {
            $perPage = (int) $raw;
        } else {
            throw ValidationException::withMessages([
                'per_page' => 'per_page değeri sayısal olmalıdır.',
            ]);
        }

        if ($perPage < 1 || $perPage > 100) {
            throw ValidationException::withMessages([
                'per_page' => 'per_page değeri 1 ile 100 arasında olmalıdır.',
            ]);
        }

        /** @var positive-int $perPage */
        return $perPage;
    }

    /**
     * @throws ValidationException
     */
    private function extractBoolean(Request $request, string $key): ?bool
    {
        $value = $request->query($key);

        if ($value === null) {
            return null;
        }

        if (\is_bool($value)) {
            return $value;
        }

        if (\is_int($value)) {
            return $value === 1;
        }

        if (\is_string($value)) {
            $normalized = strtolower(trim($value));

            if (\in_array($normalized, ['1', 'true', 'yes'], true)) {
                return true;
            }

            if (\in_array($normalized, ['0', 'false', 'no'], true)) {
                return false;
            }
        }

        throw ValidationException::withMessages([
            $key => \sprintf('%s değeri true veya false olmalıdır.', ucfirst(str_replace('_', ' ', $key))),
        ]);
    }
}
