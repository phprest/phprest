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

	/**
	 * @var Config
	 */
	private $hateoasConfig;

    public function setUp()
    {
        $this->container        = new Container();
	    $this->hateoasConfig    = new Config(true);
    }

    public function testInstansiation()
    {
        $service = new Service();
        $service->register($this->container, $this->hateoasConfig);

        $hateoasService = $this->container->get(Config::getServiceName());

        $this->assertInstanceOf('\Hateoas\Hateoas', $hateoasService);
    }

    public function testUrlGeneratorRelative()
    {
        $url = call_user_func_array($this->hateoasConfig->urlGenerator, ['/foo', [], false]);

        $this->assertEquals('/foo', $url);
    }

    public function testUrlGeneratorRelativeWithParameters()
    {
        $url = call_user_func_array($this->hateoasConfig->urlGenerator, ['/foo', ['a' => 1, 'b' => 2], false]);

        $this->assertEquals('/foo?a=1&b=2', $url);
    }

    public function testUrlGeneratorRelativeWithIdAndParameters()
    {
        $url = call_user_func_array($this->hateoasConfig->urlGenerator, ['/foo', ['id' => 5, 'name' => 'Adam'], false]);

        $this->assertEquals('/foo/5?name=Adam', $url);
    }

    public function testUrlGeneratorAbsolute()
    {
        $url = call_user_func_array($this->hateoasConfig->urlGenerator, ['/foo', ['id' => 5, 'name' => 'Adam'], true]);

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
        $service->register($this->container, $this->hateoasConfig);

        $this->assertInstanceOf('\Hateoas\Hateoas', $this->serviceHateoas());
    }

    protected function getContainer()
    {
        return $this->container;
    }
}