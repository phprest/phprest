<?php

namespace Phprest\Test\Response;

use Phprest\Response\Created;
use PHPUnit\Framework\TestCase;

class CreatedTest extends TestCase
{
    public function testInstantiation(): void
    {
        $response = new Created('http://example-location', 'test content', ['Content-Type' => 'application/json']);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('test content', $response->getContent());
        $this->assertEquals('http://example-location', $response->headers->get('location'));
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
