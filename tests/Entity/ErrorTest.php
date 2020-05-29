<?php namespace Phprest\Entity;

use Phprest\Exception\Exception;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    /**
     * @var Error
     */
    protected $error;

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
