<?php namespace Phprest\Service\Logger;

use Orno\Di\Container;
use Phprest\Stub\Service\SampleConfig;

class LoggerTest extends \PHPUnit_Framework_TestCase
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

    public function testInstansiation()
    {
        $service = new Service();
        $service->register($this->continer, new Config('sampleLoggerName'));

        $loggerService = $this->continer->get(Config::getServiceName());

        $this->assertEquals('sampleLoggerName', $loggerService->getName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithWrongConfig()
    {
        $service = new Service();
        $service->register($this->continer, new SampleConfig());
    }

    public function testGetter()
    {
        $service = new Service();
        $service->register($this->continer, new Config('anotherLoggerName'));

        $this->assertEquals('anotherLoggerName', $this->serviceLogger()->getName());
    }

    protected function getContainer()
    {
        return $this->continer;
    }
}