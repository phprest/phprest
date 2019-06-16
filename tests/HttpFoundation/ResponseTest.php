<?php namespace Phprest\HttpFoundation;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testSetContent(): void
    {
        $response = new Response('first');
        $response->setContent('second');

        $this->assertEquals('second', $response->getContent());
    }
}
