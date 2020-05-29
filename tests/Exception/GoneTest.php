<?php namespace Phprest\Exception;

use PHPUnit\Framework\TestCase;

class GoneTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new Gone(9, [1,2,3]);

        $this->assertEquals('Gone', $exception->getMessage());
        $this->assertEquals(410, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
