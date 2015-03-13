<?php namespace Phprest\Exception;

class GoneTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new Gone(9, [1,2,3]);

        $this->assertEquals('Gone', $exception->getMessage());
        $this->assertEquals(410, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
