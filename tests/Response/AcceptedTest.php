<?php namespace Phprest\Response;

use Phprest\HttpFoundation\Response;

class AcceptedTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $response = new Accepted('test content', ['Content-Type' => 'application/json']);

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals('test content', $response->getContent());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
