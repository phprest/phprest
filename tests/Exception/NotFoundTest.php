<?php

namespace Phprest\Test\Exception;

use Phprest\Exception\NotFound;
use PHPUnit\Framework\TestCase;

class NotFoundTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new NotFound(9, [1, 2, 3]);

        $this->assertEquals('Not Found', $exception->getMessage());
        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1, 2, 3], $exception->getDetails());
    }
}
