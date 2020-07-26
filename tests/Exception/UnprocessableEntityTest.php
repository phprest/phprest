<?php

namespace Phprest\Test\Exception;

use Phprest\Exception\UnprocessableEntity;
use PHPUnit\Framework\TestCase;

class UnprocessableEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new UnprocessableEntity(9, [1, 2, 3]);

        $this->assertEquals('Unprocessable Entity', $exception->getMessage());
        $this->assertEquals(422, $exception->getStatusCode());
        $this->assertEquals(9, $exception->getCode());
        $this->assertEquals([1, 2, 3], $exception->getDetails());
    }
}
