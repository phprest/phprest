<?php namespace Phprest\Exception;

class NotFoundTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new NotFound(9, [1,2,3]);

        $this->assertEquals('Not Found', $exception->getMessage());
        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
