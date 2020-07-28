<?php

namespace Phprest\Test\Exception;

use Phprest\Exception\Unauthorized;
use PHPUnit\Framework\TestCase;

class UnauthorizedTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new Unauthorized(9, [1, 2, 3]);

        $this->assertEquals('Unauthorized', $exception->getMessage());
        $this->assertEquals(401, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1, 2, 3], $exception->getDetails());
    }
}
