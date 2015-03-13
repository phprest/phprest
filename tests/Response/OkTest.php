<?php namespace Phprest\Response;

use Phprest\HttpFoundation\Response;

class OkTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $response = new Ok('test content', ['Content-Type' => 'application/json']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test content', $response->getContent());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
