<?php

namespace Phprest\Router;

use League\Container\ContainerInterface;
use League\Route\Strategy\AbstractStrategy;
use League\Route\Strategy\StrategyInterface;
use Phprest\HttpFoundation\Response;
use Phprest\Service;

class Strategy extends AbstractStrategy implements StrategyInterface
{
    use Service\Hateoas\Util;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Dispatch the controller, the return value of this method will bubble out and be
     * returned by \League\Route\Dispatcher::dispatch, it does not require a response, however,
     * beware that there is no output buffering by default in the router.
     *
     * $controller can be one of three types but based on the type you can infer what the
     * controller actually is:
     *     - string   (controller is a named function)
     *     - array    (controller is a class method [0 => ClassName, 1 => MethodName])
     *     - \Closure (controller is an anonymous function)
     *
     * @param  string|array|\Closure $controller
     * @param  array $vars - named wildcard segments of the matched route
     *
     * @return mixed
     */
    public function dispatch($controller, array $vars)
    {
        $request = $this->container->get('Symfony\Component\HttpFoundation\Request');

        $response = $this->invokeController($controller, array_merge(
            [$request],
            array_values($vars)
        ));

        if ($response instanceof Response && $response->getContent() !== '') {
            return $this->serialize(
                $response->getContent(),
                $request,
                $response
            );
        }

        return $response;
    }

    /**
     * @return \Hateoas\Hateoas
     *
     * @codeCoverageIgnore
     */
    protected function serviceHateoas()
    {
        return $this->getContainer()->get(Service\Hateoas\Config::getServiceName());
    }

    /**
     * Returns the DI container.
     *
     * @return \League\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
