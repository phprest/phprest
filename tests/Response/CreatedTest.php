<?php namespace Phprest\Response;

use Phprest\HttpFoundation\Response;

class CreatedTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $response = new Created('http://example-location', 'test content', ['Content-Type' => 'application/json']);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('test content', $response->getContent());
        $this->assertEquals('http://example-location', $response->headers->get('location'));
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
