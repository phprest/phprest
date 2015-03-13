<?php namespace Phprest\Exception;

class ForbiddenTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new Forbidden(9, [1,2,3]);

        $this->assertEquals('Forbidden', $exception->getMessage());
        $this->assertEquals(403, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
