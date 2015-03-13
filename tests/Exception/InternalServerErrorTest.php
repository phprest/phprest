<?php namespace Phprest\Exception;

class InternalServerErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new InternalServerError(9, [1,2,3]);

        $this->assertEquals('Internal Server Error', $exception->getMessage());
        $this->assertEquals(500, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
