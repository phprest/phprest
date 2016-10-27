<?php namespace Phprest\Router;

use Phprest\Application;
use Phprest\Service;
use League\Container\Container;
use Symfony\Component\HttpFoundation\Request;

class StrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Strategy
     */
    private $strategy;

    public function setUp()
    {
        $this->container = new Container();
        $this->strategy = new Strategy($this->container);
    }

    public function testDispatchWithClosure()
    {
        $result = $this->strategy->dispatch(
            function()
            {
                return 42;
            },
            []
        );

        $this->assertEquals(42, $result);
    }

    public function testDispatchWithClassAndMethod()
    {
        $result = $this->strategy->dispatch(
            'Phprest\Stub\Controller\Simple::getTheAnswerOfEverything',
            []
        );

        $this->assertEquals(42, $result);
    }

    public function testDispatchWithClassAndMethodAndResponseObject()
    {
        $this->setRequestParameters('phprest-test', 1, '*/*');

        $result = $this->strategy->dispatch(
            'Phprest\Stub\Controller\Simple::getSampleResponse',
            []
        );

        $this->assertInstanceOf('Phprest\HttpFoundation\Response', $result);
        if ($result instanceof \Phprest\HttpFoundation\Response) {
            $this->assertEquals(json_encode('sample'), $result->getContent());
        }
    }

    /**
     * @param string $vendor
     * @param string|integer $apiVersion
     * @param string $acceptHeader
     */
    protected function setRequestParameters($vendor, $apiVersion, $acceptHeader)
    {
        $this->container->add(Application::CONTAINER_ID_VENDOR, $vendor);
        $this->container->add(Application::CONTAINER_ID_API_VERSION, $apiVersion);

        (new Service\Hateoas\Service())->
            register($this->container, new Service\Hateoas\Config(true));

        $request = new Request();
        $request->headers->set('Accept', $acceptHeader, true);

        $this->container->add('Symfony\Component\HttpFoundation\Request', $request);
    }
}
