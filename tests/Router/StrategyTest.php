<?php

namespace Phprest\Test\Router;

use Phprest\Application;
use Phprest\Router\Strategy;
use Phprest\Service;
use League\Container\Container;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Phprest\HttpFoundation\Response;

class StrategyTest extends TestCase
{
    private Container $container;
    private Strategy $strategy;

    public function setUp(): void
    {
        $this->container = new Container();
        $this->strategy = new Strategy($this->container);
    }

    public function testDispatchWithClosure(): void
    {
        $result = $this->strategy->dispatch(
            static function () {
                return 42;
            },
            []
        );

        $this->assertEquals(42, $result);
    }

    public function testDispatchWithClassAndMethod(): void
    {
        $result = $this->strategy->dispatch(
            'Phprest\Stub\Controller\Simple::getTheAnswerOfEverything',
            []
        );

        $this->assertEquals(42, $result);
    }

    public function testDispatchWithClassAndMethodAndResponseObject(): void
    {
        $this->setRequestParameters('phprest-test', 1, '*/*');

        $result = $this->strategy->dispatch(
            'Phprest\Stub\Controller\Simple::getSampleResponse',
            []
        );

        $this->assertInstanceOf(Response::class, $result);
        if ($result instanceof Response) {
            $this->assertEquals(json_encode('sample'), $result->getContent());
        }
    }

    /**
     * @param string $vendor
     * @param string|integer $apiVersion
     * @param string $acceptHeader
     */
    protected function setRequestParameters($vendor, $apiVersion, $acceptHeader): void
    {
        $this->container->add(Application::CONTAINER_ID_VENDOR, $vendor);
        $this->container->add(Application::CONTAINER_ID_API_VERSION, $apiVersion);

        (new Service\Hateoas\Service())->
        register($this->container, new Service\Hateoas\Config(true));

        $request = new Request();
        $request->headers->set('Accept', $acceptHeader, true);

        $this->container->add(Request::class, $request);
    }
}
