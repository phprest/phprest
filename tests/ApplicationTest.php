<?php

namespace Phprest;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Phprest\Router\RouteCollection;
use League\Container\ContainerInterface;
use Phprest\Stub\Controller\Simple;

class ApplicationTest extends TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Application
     */
    protected $app;

    public function setUp(): void
    {
        $this->config = new Config('phprest-test', 1, true);
        $this->app = new Application($this->config);
    }

    public function testInstantiation(): void
    {
        $this->assertTrue(
            $this->app->getContainer()->isSingleton(Service\Hateoas\Config::getServiceName())
        );
        $this->assertTrue(
            $this->app->getContainer()->isSingleton(Service\Logger\Config::getServiceName())
        );
        $this->assertEquals('phprest-test', $this->app->getContainer()->get(Application::CONTAINER_ID_VENDOR));
        $this->assertEquals(1, $this->app->getContainer()->get(Application::CONTAINER_ID_API_VERSION));
        $this->assertTrue($this->app->getContainer()->get(Application::CONTAINER_ID_DEBUG));
        $this->assertInstanceOf(
            RouteCollection::class,
            $this->app->getContainer()->get(Application::CONTAINER_ID_ROUTER)
        );
    }

    public function testRun(): void
    {
        $request = Request::create('/welcome');
        $request->headers->set('Accept', '*/*');

        $this->app->get('/1.0/welcome', static function (Request $request) {
            return new Response('Hello Phprest World', 200);
        });

        ob_start();
        $this->app->run($request);

        $this->assertEquals('Hello Phprest World', ob_get_clean());
    }

    public function testRunNotFound(): void
    {
        $this->expectException(\League\Route\Http\Exception\NotFoundException::class);
        $this->app->run();
    }

    public function testRegisterController(): void
    {
        $this->app->registerController(Simple::class);

        $this->assertInstanceOf(
            Simple::class,
            $this->app->getContainer()->get(Simple::class)
        );
    }

    public function testHead(): void
    {
        $this->app->head('/test-head', 'test-handler');

        $this->assertEquals(
            ['method' => 'HEAD', 'route' => '/test-head', 'handler' => 'test-handler'],
            $this->app->getRouter()->getRoutingTable()[0]
        );
    }

    public function testOptions(): void
    {
        $this->app->options('/test-options', 'test-handler');

        $this->assertEquals(
            ['method' => 'OPTIONS', 'route' => '/test-options', 'handler' => 'test-handler'],
            $this->app->getRouter()->getRoutingTable()[0]
        );
    }

    public function testGetters(): void
    {
        $this->assertInstanceOf(Config::class, $this->app->getConfiguration());
        $this->assertInstanceOf(ContainerInterface::class, $this->app->getContainer());
        $this->assertInstanceOf(RouteCollection::class, $this->app->getRouter());
    }
}
