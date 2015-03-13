<?php namespace Phprest\Exception;

class TooManyRequestsTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new TooManyRequests(9, [1,2,3]);

        $this->assertEquals('Too Many Requests', $exception->getMessage());
        $this->assertEquals(429, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
