<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\SanitizedResource;

class FakeSanitizedResource extends SanitizedResource
{
    public function publicSanitize(?string $value): ?string
    {
        return $this->sanitize($value);
    }

    public function publicSanitizeArray(array $data): array
    {
        return $this->sanitizeArray($data);
    }
}
