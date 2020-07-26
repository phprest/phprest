<?php

namespace Phprest\Test\Exception;

use Phprest\Exception\TooManyRequests;
use PHPUnit\Framework\TestCase;

class TooManyRequestsTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new TooManyRequests(9, [1, 2, 3]);

        $this->assertEquals('Too Many Requests', $exception->getMessage());
        $this->assertEquals(429, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1, 2, 3], $exception->getDetails());
    }
}
