<?php

namespace Tests\Unit\Http\Requests;

use App\Support\Traits\SanitizesInput;
use Tests\TestCase;

class SanitizesInputTraitTest extends TestCase
{
    use SanitizesInput;
    public function test_sanitize_a_form_request() {
        $dirty = ['key'=>'<script>alert("xss")</script>'];
        $clean = $this->sanitizeData($dirty);

        $this->assertEquals(htmlspecialchars($dirty['key'], ENT_QUOTES, 'UTF-8'), $clean['key']);
    }
}
