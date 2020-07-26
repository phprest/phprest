<?php

namespace Phprest\Test\Exception;

use Phprest\Exception\NotAcceptable;
use PHPUnit\Framework\TestCase;

class NotAcceptableTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new NotAcceptable(9, [1, 2, 3]);

        $this->assertEquals('Not Acceptable', $exception->getMessage());
        $this->assertEquals(406, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1, 2, 3], $exception->getDetails());
    }
}
