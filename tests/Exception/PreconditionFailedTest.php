<?php namespace Phprest\Exception;

class PreconditionFailedTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new PreconditionFailed(9, [1,2,3]);

        $this->assertEquals('Precondition Failed', $exception->getMessage());
        $this->assertEquals(412, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
