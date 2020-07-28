<?php

namespace Phprest\Test\ErrorHandler\Handler;

use ErrorException;
use Exception;
use Phprest\ErrorHandler\Handler\Log;
use Phprest\Exception\BadRequest;
use PHPUnit\Framework\TestCase;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

class LogTest extends TestCase
{
    protected TestHandler $monologHandler;
    protected Logger $monolog;
    protected Log $logHandler;

    public function setUp(): void
    {
        $this->monologHandler = new TestHandler();
        $this->monolog = new Logger('test', [$this->monologHandler]);
        $this->logHandler = new Log($this->monolog);
    }

    public function testSimpleException(): void
    {
        $this->assertFalse($this->monologHandler->hasCriticalRecords());

        $this->logHandler->handle(new Exception('test exception'));

        $this->assertTrue($this->monologHandler->hasCriticalRecords());
    }

    public function testPhprestException(): void
    {
        $this->assertFalse($this->monologHandler->hasCriticalRecords());

        $this->logHandler->handle(new BadRequest(9, ['a detail']));

        $this->assertTrue($this->monologHandler->hasCriticalRecords());
    }

    public function testErrorExceptionErrorLog(): void
    {
        $this->assertFalse($this->monologHandler->hasErrorRecords());

        $this->logHandler->handle(new ErrorException('test exception', 0, E_ERROR));

        $this->assertTrue($this->monologHandler->hasErrorRecords());
    }

    public function testWarningExceptionErrorLog(): void
    {
        $this->assertFalse($this->monologHandler->hasWarningRecords());

        $this->logHandler->handle(new ErrorException('test exception', 0, E_WARNING));

        $this->assertTrue($this->monologHandler->hasWarningRecords());
    }

    public function testNoticeExceptionErrorLog(): void
    {
        $this->assertFalse($this->monologHandler->hasNoticeRecords());

        $this->logHandler->handle(new ErrorException('test exception', 0, E_NOTICE));

        $this->assertTrue($this->monologHandler->hasNoticeRecords());
    }

    public function testInfoExceptionErrorLog(): void
    {
        $this->assertFalse($this->monologHandler->hasInfoRecords());

        $this->logHandler->handle(new ErrorException('test exception', 0, E_STRICT));

        $this->assertTrue($this->monologHandler->hasInfoRecords());
    }
}
