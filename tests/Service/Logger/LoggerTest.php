<?php namespace Phprest\Service\Logger;

use InvalidArgumentException;
use League\Container\Container;
use Phprest\Stub\Service\SampleConfig;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    use Getter;

    /**
     * @var Container
     */
    private $continer;

    public function setUp()
    {
        $this->continer = new Container();
    }

    public function testInstansiation(): void
    {
        $service = new Service();
        $service->register($this->continer, new Config('sampleLoggerName'));

        $loggerService = $this->continer->get(Config::getServiceName());

        $this->assertEquals('sampleLoggerName', $loggerService->getName());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithWrongConfig(): void
    {
        $service = new Service();
        $service->register($this->continer, new SampleConfig());
    }

    public function testGetter(): void
    {
        $service = new Service();
        $service->register($this->continer, new Config('anotherLoggerName'));

        $this->assertEquals('anotherLoggerName', $this->serviceLogger()->getName());
    }

    protected function getContainer(): Container
    {
        return $this->continer;
    }
}
