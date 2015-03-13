<?php namespace Phprest\Exception;

class BadRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new BadRequest(9, [1,2,3]);

        $this->assertEquals('Bad Request', $exception->getMessage());
        $this->assertEquals(400, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
