<?php

namespace Phprest\Test\Exception;

use Phprest\Exception\InternalServerError;
use PHPUnit\Framework\TestCase;

class InternalServerErrorTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new InternalServerError(9, [1, 2, 3]);

        $this->assertEquals('Internal Server Error', $exception->getMessage());
        $this->assertEquals(500, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1, 2, 3], $exception->getDetails());
    }
}
