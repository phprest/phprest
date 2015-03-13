<?php namespace Phprest\Exception;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new Exception('test message', 9, 201, [1,2,3]);

        $this->assertEquals('test message', $exception->getMessage());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals(201, $exception->getStatusCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
