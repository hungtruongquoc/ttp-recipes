<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SanitizingFormRequest extends FormRequest
{
    /**
     * Get the validated data from the request with sanitization applied.
     *
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated($key, $default);

        if (!is_null($key)) {
            return $this->sanitizeField($validated);
        }

        return $this->sanitizeData($validated);
    }

    /**
     * Sanitize a single field.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function sanitizeField(mixed $value): mixed
    {
        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        return $value;
    }

    /**
     * Sanitize all data recursively.
     *
     * @param array $data
     * @return array
     */
    protected function sanitizeData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeData($value);
            }
        }

        return $data;
    }
}
