<?php namespace Phprest\Response;

use Phprest\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;

class OkTest extends TestCase
{
    public function testInstantiation(): void
    {
        $response = new Ok('test content', ['Content-Type' => 'application/json']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test content', $response->getContent());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
