<?php namespace Phprest\Response;

use Phprest\HttpFoundation\Response;

class NotModifiedTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $response = new NotModified('http://conent-location', 'ofk48fh1ubuc', ['Content-Type' => 'application/json']);

        $this->assertEquals(304, $response->getStatusCode());
        $this->assertEquals('http://conent-location', $response->headers->get('content-location'));
        $this->assertEquals('ofk48fh1ubuc', $response->headers->get('etag'));
    }
}
