<?php namespace Phprest\Exception;

class UnauthorizedTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new Unauthorized(9, [1,2,3]);

        $this->assertEquals('Unauthorized', $exception->getMessage());
        $this->assertEquals(401, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
