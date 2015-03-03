<?php namespace Phprest\Service\Hateoas;

use Phprest\Stub\Service\SampleConfig;
use League\Container\Container;

class HateoasTest extends \PHPUnit_Framework_TestCase
{
    use Getter;

    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function testInstansiation()
    {
        $service = new Service();
        $service->register($this->container, new Config());

        $hateoasService = $this->container->get(Config::getServiceName());

        $this->assertInstanceOf('\Hateoas\Hateoas', $hateoasService);
    }

    public function testUrlGeneratorRelative()
    {
        $config = new Config();

        $url = call_user_func_array($config->urlGenerator, ['/foo', [], false]);

        $this->assertEquals('/foo', $url);
    }

    public function testUrlGeneratorRelativeWithParameters()
    {
        $config = new Config();

        $url = call_user_func_array($config->urlGenerator, ['/foo', ['a' => 1, 'b' => 2], false]);

        $this->assertEquals('/foo?a=1&b=2', $url);
    }

    public function testUrlGeneratorRelativeWithIdAndParameters()
    {
        $config = new Config();

        $url = call_user_func_array($config->urlGenerator, ['/foo', ['id' => 5, 'name' => 'Adam'], false]);

        $this->assertEquals('/foo/5?name=Adam', $url);
    }

    public function testUrlGeneratorAbsolute()
    {
        $config = new Config();

        $url = call_user_func_array($config->urlGenerator, ['/foo', ['id' => 5, 'name' => 'Adam'], true]);

        $this->assertEquals('http://:/foo/5?name=Adam', $url); # no host and port in CLI
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithWrongConfig()
    {
        $service = new Service();
        $service->register($this->container, new SampleConfig());
    }

    public function testGetter()
    {
        $service = new Service();
        $service->register($this->container, new Config());

        $this->assertInstanceOf('\Hateoas\Hateoas', $this->serviceHateoas());
    }

    protected function getContainer()
    {
        return $this->container;
    }
}