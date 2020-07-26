<?php

namespace Phprest\Test\Response;

use Phprest\Response\Accepted;
use PHPUnit\Framework\TestCase;

class AcceptedTest extends TestCase
{
    public function testInstantiation(): void
    {
        $response = new Accepted('test content', ['Content-Type' => 'application/json']);

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals('test content', $response->getContent());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
