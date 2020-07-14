<?php

namespace Phprest\Test\Exception;

use Phprest\Exception\Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new Exception('test message', 9, 201, [1, 2, 3]);

        $this->assertEquals('test message', $exception->getMessage());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals(201, $exception->getStatusCode());
        $this->assertEquals([1, 2, 3], $exception->getDetails());
    }
}
