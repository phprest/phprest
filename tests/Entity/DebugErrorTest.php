<?php namespace Phprest\Entity;

use Exception;
use PHPUnit\Framework\TestCase;

class DebugErrorTest extends TestCase
{
    /**
     * @var DebugError
     */
    protected $debugError;

    public function setUp(): void
    {
        $this->debugError = new DebugError(new Exception('exception message', 101));
    }

    public function testGetFileName(): void
    {
        $this->assertEquals('DebugErrorTest.php', basename($this->debugError->getFileName()));
    }

    public function testGetLine(): void
    {
        $this->assertEquals(15, $this->debugError->getLine());
    }

    public function testGetTrace(): void
    {
        $this->assertNotNull($this->debugError->getTrace());
    }
}
