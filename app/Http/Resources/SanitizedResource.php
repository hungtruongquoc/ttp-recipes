<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SanitizedResource extends JsonResource
{
    /**
     * Sanitize a string value.
     *
     * @param string|null $value
     * @return string|null
     */
    protected function sanitize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize all string values in an array recursively.
     *
     * @param array $data
     * @return array
     */
    protected function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = $this->sanitize($value);
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            }
        }

        return $data;
    }
}
