<?php namespace Phprest\Exception;

use PHPUnit\Framework\TestCase;

class BadRequestTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new BadRequest(9, [1,2,3]);

        $this->assertEquals('Bad Request', $exception->getMessage());
        $this->assertEquals(400, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
