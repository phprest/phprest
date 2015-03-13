<?php namespace Phprest\Exception;

class UnprocessableEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $exception = new UnprocessableEntity(9, [1,2,3]);

        $this->assertEquals('Unprocessable Entity', $exception->getMessage());
        $this->assertEquals(422, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1,2,3], $exception->getDetails());
    }
}
