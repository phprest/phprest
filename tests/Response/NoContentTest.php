<?php namespace Phprest\Response;

use Phprest\HttpFoundation\Response;

class NoContentTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $response = new NoContent(['Content-Type' => 'application/json']);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
