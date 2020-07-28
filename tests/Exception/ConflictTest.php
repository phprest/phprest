<?php

namespace Phprest\Test\Exception;

use Phprest\Exception\Conflict;
use PHPUnit\Framework\TestCase;

class ConflictTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new Conflict(9, [1, 2, 3]);

        $this->assertEquals('Conflict', $exception->getMessage());
        $this->assertEquals(409, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1, 2, 3], $exception->getDetails());
    }
}
