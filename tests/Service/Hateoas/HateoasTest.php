<?php

namespace Phprest\Test\Service\Hateoas;

use InvalidArgumentException;
use Phprest\Service\Hateoas\Config;
use Phprest\Service\Hateoas\Getter;
use Phprest\Service\Hateoas\Service;
use Phprest\Stub\Service\SampleConfig;
use League\Container\Container;
use Hateoas\Hateoas;
use PHPUnit\Framework\TestCase;

class HateoasTest extends TestCase
{
    use Getter;

    private Container $container;
    private Config $hateoasConfig;

    public function setUp(): void
    {
        $this->container = new Container();
        $this->hateoasConfig = new Config(true);
    }

    public function testInstansiation(): void
    {
        $service = new Service();
        $service->register($this->container, $this->hateoasConfig);

        $hateoasService = $this->container->get(Config::getServiceName());

        $this->assertInstanceOf(Hateoas::class, $hateoasService);
    }

    public function testUrlGeneratorRelative(): void
    {
        $url = call_user_func($this->hateoasConfig->urlGenerator, '/foo', [], false);

        $this->assertEquals('/foo', $url);
    }

    public function testUrlGeneratorRelativeWithParameters(): void
    {
        $url = call_user_func($this->hateoasConfig->urlGenerator, '/foo', ['a' => 1, 'b' => 2], false);

        $this->assertEquals('/foo?a=1&b=2', $url);
    }

    public function testUrlGeneratorRelativeWithIdAndParameters(): void
    {
        $url = call_user_func($this->hateoasConfig->urlGenerator, '/foo', ['id' => 5, 'name' => 'Adam'], false);

        $this->assertEquals('/foo/5?name=Adam', $url);
    }

    public function testUrlGeneratorAbsolute(): void
    {
        $url = call_user_func($this->hateoasConfig->urlGenerator, '/foo', ['id' => 5, 'name' => 'Adam'], true);

        $this->assertEquals('http://:/foo/5?name=Adam', $url); # no host and port in CLI
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
        $service->register($this->container, $this->hateoasConfig);

        $this->assertInstanceOf(Hateoas::class, $this->serviceHateoas());
    }

    protected function getContainer(): Container
    {
        return $this->container;
    }
}
