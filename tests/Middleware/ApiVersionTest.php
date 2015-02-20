<?php namespace Phprest\Middleware;

use Phprest\Application;
use Phprest\Config;
use Symfony\Component\HttpFoundation\Request;

class ApiVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider appProvider
     *
     * @param Application $app
     */
    public function testHandle(Application $app)
    {
        $middleware = new ApiVersion($app);

        /** @var \Mockery\MockInterface $app */
        $app->shouldReceive('handle')->andReturnUsing(function($request) {
            $this->assertInstanceOf('Phprest\HttpFoundation\Request', $request);

            /** @var \Phprest\HttpFoundation\Request $request */
            $this->assertEquals('/2.6/temperatures', $request->getPathInfo());
        });

        $middleware->handle(
            Request::create('/temperatures')
        );
    }

    public function appProvider()
    {
        $app = \Mockery::mock('Phprest\Application');

        $config = new Config('test', '2.6');
        $config->getContainer()->add(Application::CNTRID_VENDOR, $config->getVendor());
        $config->getContainer()->add(Application::CNTRID_API_VERSION, $config->getApiVersion());
        $config->getContainer()->add(Application::CNTRID_DEBUG, $config->isDebug());

        $app->shouldReceive('getConfig')->andReturn($config);
        $app->shouldReceive('getContainer')->andReturn($config->getContainer());

        return [[$app]];
    }

    protected function tearDown()
    {
        \Mockery::close();
    }
}