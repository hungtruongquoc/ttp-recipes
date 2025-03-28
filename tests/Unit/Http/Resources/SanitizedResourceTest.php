<?php
namespace Tests\Unit\Http\Resources;

use Tests\TestCase;

class SanitizedResourceTest extends TestCase
{
    public function test_it_sanitizes_single_string()
    {
        $resource = new FakeSanitizedResource([]);
        $dirty = '<script>alert("xss")</script>';
        $clean = $resource->publicSanitize($dirty);

        $this->assertEquals(htmlspecialchars($dirty, ENT_QUOTES, 'UTF-8'), $clean);
    }

    public function test_it_handles_null_strings()
    {
        $resource = new FakeSanitizedResource([]);
        $clean = $resource->publicSanitize(null);

        $this->assertNull($clean);
    }

    public function test_it_recursively_sanitizes_array()
    {
        $resource = new FakeSanitizedResource([]);
        $dirty = [
            'title' => '<b>hello</b>',
            'nested' => [
                'script' => '<script>alert("bad")</script>'
            ]
        ];

        $clean = $resource->publicSanitizeArray($dirty);

        $this->assertEquals([
            'title' => htmlspecialchars('<b>hello</b>', ENT_QUOTES, 'UTF-8'),
            'nested' => [
                'script' => htmlspecialchars('<script>alert("bad")</script>', ENT_QUOTES, 'UTF-8'),
            ]
        ], $clean);
    }
}
