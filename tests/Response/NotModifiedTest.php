<?php
namespace Phprest\Response;

use PHPUnit\Framework\TestCase;

class NotModifiedTest extends TestCase
{
    public function testInstantiation(): void
    {
        $response = new NotModified('http://conent-location', 'ofk48fh1ubuc', ['Content-Type' => 'application/json']);

        $this->assertEquals(304, $response->getStatusCode());
        $this->assertEquals('http://conent-location', $response->headers->get('content-location'));
        $this->assertEquals('ofk48fh1ubuc', $response->headers->get('etag'));
    }
}
