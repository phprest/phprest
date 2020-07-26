<?php

namespace Phprest\Test\Service\Logger;

use InvalidArgumentException;
use League\Container\Container;
use Phprest\Service\Logger\Config;
use Phprest\Service\Logger\Getter;
use Phprest\Service\Logger\Service;
use Phprest\Stub\Service\SampleConfig;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    use Getter;

    private Container $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testInstansiation(): void
    {
        $service = new Service();
        $service->register($this->container, new Config('sampleLoggerName'));

        $loggerService = $this->container->get(Config::getServiceName());

        $this->assertEquals('sampleLoggerName', $loggerService->getName());
    }

    public function testWithWrongConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $service = new Service();
        $service->register($this->container, new SampleConfig());
    }

    public function testGetter(): void
    {
        $service = new Service();
        $service->register($this->container, new Config('anotherLoggerName'));

        $this->assertEquals('anotherLoggerName', $this->serviceLogger()->getName());
    }

    protected function getContainer(): Container
    {
        return $this->container;
    }
}
