<?php namespace Phprest\Entity;

use Phprest\Exception\Exception;

class DebugErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DebugError
     */
    protected $debugError = null;

    public function setUp()
    {
        $this->debugError = new DebugError(new \Exception('exception message', 101));
    }

    public function testGetFileName()
    {
        $this->assertEquals('DebugErrorTest.php', basename($this->debugError->getFileName()));
    }

    public function testGetLine()
    {
        $this->assertEquals(14, $this->debugError->getLine());
    }

    public function testGetTrace()
    {
        $this->assertNotNull($this->debugError->getTrace());
    }
}
