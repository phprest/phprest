<?php namespace Phprest;

use Phprest\Application;
use Phprest\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Application
     */
    protected $app;

    public function setUp()
    {
        $this->config = new Config('phprest-test', 1, true);
        $this->app = new Application($this->config);
    }

    public function testInstantiation()
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
        $this->assertInstanceOf('\Phprest\Router\RouteCollection', $this->app->getContainer()->get(Application::CONTAINER_ID_ROUTER));
    }

    public function testRun()
    {
        $request = Request::create('/welcome');
        $request->headers->set('Accept', '*/*');

        $this->app->get('/1.0/welcome', function (Request $request) {
            return new Response('Hello Phprest World', 200);
        });

        ob_start();
        $this->app->run($request);

        $this->assertEquals('Hello Phprest World', ob_get_clean());
    }

    /**
     * @expectedException \League\Route\Http\Exception\NotFoundException
     */
    public function testRunNotFound()
    {
        $this->app->run();
    }

    public function testRegisterController()
    {
        $this->app->registerController('\Phprest\Stub\Controller\Simple');

        $this->assertInstanceOf(
            '\Phprest\Stub\Controller\Simple',
            $this->app->getContainer()->get('\Phprest\Stub\Controller\Simple')
        );
    }

    public function testHead()
    {
        $this->app->head('/test-head', 'test-handler');

        $this->assertEquals(
            ['method' => 'HEAD', 'route' => '/test-head', 'handler' => 'test-handler'],
            $this->app->getRouter()->getRoutingTable()[0]
        );
    }

    public function testOptions()
    {
        $this->app->options('/test-options', 'test-handler');

        $this->assertEquals(
            ['method' => 'OPTIONS', 'route' => '/test-options', 'handler' => 'test-handler'],
            $this->app->getRouter()->getRoutingTable()[0]
        );
    }

    public function testGetters()
    {
        $this->assertInstanceOf('\Phprest\Config', $this->app->getConfiguration());
        $this->assertInstanceOf('\League\Container\ContainerInterface', $this->app->getContainer());
        $this->assertInstanceOf('\Phprest\Router\RouteCollection', $this->app->getRouter());
    }
}
