<?php namespace Phprest\Exception;

class ConflictTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new Conflict(9, [1,2,3]);

        $this->assertEquals('Conflict', $exception->getMessage());
        $this->assertEquals(409, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
