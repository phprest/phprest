<?php

namespace Phprest\Test\HttpFoundation;

use Phprest\HttpFoundation\Response;
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
