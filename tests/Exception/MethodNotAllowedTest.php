<?php

namespace Phprest\Test\Exception;

use Phprest\Exception\MethodNotAllowed;
use PHPUnit\Framework\TestCase;

class MethodNotAllowedTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new MethodNotAllowed(9, [1, 2, 3]);

        $this->assertEquals('Method Not Allowed', $exception->getMessage());
        $this->assertEquals(405, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1, 2, 3], $exception->getDetails());
    }
}
