<?php namespace Phprest\Response;

use Phprest\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;

class NoContentTest extends TestCase
{
    public function testInstantiation(): void
    {
        $response = new NoContent(['Content-Type' => 'application/json']);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
