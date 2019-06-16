<?php namespace Phprest\Middleware;

use Mockery;
use Mockery\MockInterface;
use Phprest\Application;
use Phprest\Config;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ApiVersionTest extends TestCase
{
    /**
     * @dataProvider appProvider
     *
     * @param Application $app
     */
    public function testHandle(Application $app): void
    {
        $middleware = new ApiVersion($app);

        /** @var MockInterface $app */
        $app->shouldReceive('handle')->andReturnUsing(function ($request) {
            $this->assertInstanceOf(\Phprest\HttpFoundation\Request::class, $request);

            /** @var \Phprest\HttpFoundation\Request $request */
            $this->assertEquals('/2.6/temperatures', $request->getPathInfo());
        });

        $middleware->handle(
            Request::create('/temperatures')
        );
    }

    public function appProvider(): array
    {
        $app = Mockery::mock(Application::class);

        $config = new Config('test', '2.6');
        $config->getContainer()->add(Application::CONTAINER_ID_VENDOR, $config->getVendor());
        $config->getContainer()->add(Application::CONTAINER_ID_API_VERSION, $config->getApiVersion());
        $config->getContainer()->add(Application::CONTAINER_ID_DEBUG, $config->isDebug());

        $app->shouldReceive('getConfiguration')->andReturn($config);
        $app->shouldReceive('getContainer')->andReturn($config->getContainer());

        return [[$app]];
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}
