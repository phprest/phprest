<?php namespace Phprest\Exception;

class MethodNotAllowedTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new MethodNotAllowed(9, [1,2,3]);

        $this->assertEquals('Method Not Allowed', $exception->getMessage());
        $this->assertEquals(405, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
