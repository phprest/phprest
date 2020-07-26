<?php

namespace Phprest\Test\Entity;

use Exception;
use Phprest\Entity\DebugError;
use PHPUnit\Framework\TestCase;

class DebugErrorTest extends TestCase
{
    protected DebugError $debugError;

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
