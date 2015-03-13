<?php namespace Phprest\ErrorHandler\Handler;

use Phprest\Exception\BadRequest;
use Phprest\Application;
use Phprest\Config;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

class LogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestHandler
     */
    protected $monologHandler;

    /**
     * @var Logger
     */
    protected $monolog;

    /**
     * @var Log
     */
    protected $logHandler;

    public function setUp()
    {
        $this->monologHandler = new TestHandler();

        $this->monolog = new Logger('test', [$this->monologHandler]);

        $this->logHandler = new Log($this->monolog);
    }

    public function testSimpleException()
    {
        $this->assertFalse($this->monologHandler->hasCriticalRecords());

        $this->logHandler->handle(new \Exception('test exception'));

        $this->assertTrue($this->monologHandler->hasCriticalRecords());
    }

    public function testPhprestException()
    {
        $this->assertFalse($this->monologHandler->hasCriticalRecords());

        $this->logHandler->handle(new BadRequest(9, ['a detail']));

        $this->assertTrue($this->monologHandler->hasCriticalRecords());
    }

    public function testErrorExceptionErrorLog()
    {
        $this->assertFalse($this->monologHandler->hasErrorRecords());

        $this->logHandler->handle(new \ErrorException('test exception', 0, E_ERROR));

        $this->assertTrue($this->monologHandler->hasErrorRecords());
    }

    public function testWarningExceptionErrorLog()
    {
        $this->assertFalse($this->monologHandler->hasWarningRecords());

        $this->logHandler->handle(new \ErrorException('test exception', 0, E_WARNING));

        $this->assertTrue($this->monologHandler->hasWarningRecords());
    }

    public function testNoticeExceptionErrorLog()
    {
        $this->assertFalse($this->monologHandler->hasNoticeRecords());

        $this->logHandler->handle(new \ErrorException('test exception', 0, E_NOTICE));

        $this->assertTrue($this->monologHandler->hasNoticeRecords());
    }

    public function testInfoExceptionErrorLog()
    {
        $this->assertFalse($this->monologHandler->hasInfoRecords());

        $this->logHandler->handle(new \ErrorException('test exception', 0, E_STRICT));

        $this->assertTrue($this->monologHandler->hasInfoRecords());
    }
}
