<?php namespace Phprest\Entity;

use Phprest\Exception\Exception;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Error
     */
    protected $error = null;

    public function setUp()
    {
        $this->error = new Error(new \Exception('exception message', 101));
    }

    public function testGetCode()
    {
        $this->assertEquals(101, $this->error->getCode());
    }

    public function testGetMessage()
    {
        $this->assertEquals('exception message', $this->error->getMessage());
    }

    public function testGetDetails()
    {
        $error = new Error(new Exception('', 0, 500, ['details']));

        $this->assertEquals(['details'], $error->getDetails());
    }
}
