<?php namespace Phprest\Exception;

class UnsupportedMediaTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new UnsupportedMediaType(9, [1,2,3]);

        $this->assertEquals('Unsupported Media Type', $exception->getMessage());
        $this->assertEquals(415, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
