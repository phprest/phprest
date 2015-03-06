<?php namespace Phprest\Router;

use Phprest\HttpFoundation\Response;
use Phprest\Service;
use League\Route\Strategy\StrategyInterface;
use League\Route\Strategy\AbstractStrategy;
use League\Container\Container;
use League\Route\Http\Exception as HttpException;
use Symfony\Component\HttpFoundation\Request;

class Strategy extends AbstractStrategy implements StrategyInterface
{
    use Service\Hateoas\Util;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Dispatch the controller, the return value of this method will bubble out and be
     * returned by \League\Route\Dispatcher::dispatch, it does not require a response, however,
     * beware that there is no output buffering by default in the router
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
        $request = Request::createFromGlobals();

        $response = $this->invokeController($controller, array_merge(
            [$request],
            array_values($vars)
        ));

        if ($response instanceof Response and $response->getContent() !== '') {
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
     * Returns the DI container
     *
     * @return \League\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
