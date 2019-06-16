<?php namespace Phprest\Exception;

use PHPUnit\Framework\TestCase;

class ForbiddenTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new Forbidden(9, [1,2,3]);

        $this->assertEquals('Forbidden', $exception->getMessage());
        $this->assertEquals(403, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
