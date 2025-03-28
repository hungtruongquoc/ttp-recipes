<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Traits\SanitizesInput;

class SanitizingFormRequest extends FormRequest
{
    use SanitizesInput;
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
}
