<?php

namespace Phprest\Test\Entity;

use Phprest\Entity\Error;
use Phprest\Exception\Exception;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    protected Error $error;

    public function setUp(): void
    {
        $this->error = new Error(new \Exception('exception message', 101));
    }

    public function testGetCode(): void
    {
        $this->assertEquals(101, $this->error->getCode());
    }

    public function testGetMessage(): void
    {
        $this->assertEquals('exception message', $this->error->getMessage());
    }

    public function testGetDetails(): void
    {
        $error = new Error(new Exception('', 0, 500, ['details']));

        $this->assertEquals(['details'], $error->getDetails());
    }
}
