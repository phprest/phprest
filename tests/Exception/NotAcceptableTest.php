<?php namespace Phprest\Exception;

class NotAcceptableTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new NotAcceptable(9, [1,2,3]);

        $this->assertEquals('Not Acceptable', $exception->getMessage());
        $this->assertEquals(406, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
