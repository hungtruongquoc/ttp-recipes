<?php

namespace App\Support\Traits;

trait SanitizesInput
{
    protected function sanitizeField(mixed $value): mixed
    {
        return is_string($value)
            ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
            : $value;
    }

    protected function sanitizeData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = $this->sanitizeField($value);
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeData($value);
            }
        }

        return $data;
    }
}
