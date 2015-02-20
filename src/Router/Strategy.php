<?php namespace Phprest\Router;

use Phprest\HttpFoundation\Response;
use Phprest\Service;
use Orno\Route\CustomStrategyInterface;
use Orno\Di\Container;

class Strategy implements CustomStrategyInterface
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
     * returned by \Orno\Route\Dispatcher::dispatch, it does not require a response, however,
     * beware that there is no output buffering by default in the router
     *
     * $controller can be one of three types but based on the type you can infer what the
     * controller actually is:
     *     - string   (controller is a named function)
     *     - array    (controller is a class method [0 => ClassName, 1 => MethodName])
     *     - \Closure (controller is an anonymous function)
     *
     * @param string|array|\Closure $handler
     * @param array $vars - named wildcard segments of the matched route
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function dispatch($handler, array $vars)
    {
        $controller = null;

        // figure out what the controller is
        if (($handler instanceof \Closure) || (is_string($handler) && is_callable($handler))) {
            $controller = $handler;
        }

        if (is_string($handler) && strpos($handler, '::') !== false) {
            $controller = explode('::', $handler);
        }

        // if controller method wasn't specified, throw exception.
        if ( ! $controller){
            throw new \RuntimeException('A class method must be provided as a controller. ClassName::methodName');
        }

        $request = $this->getContainer()->get('Orno\Http\Request');

        $response = $this->invokeController($controller, array_merge([$request], $vars));

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
     * Invoke a controller action
     *
     * @param string|\Closure $controller
     * @param array $vars
     *
     * @return \Orno\Http\ResponseInterface
     */
    protected  function invokeController($controller, array $vars = [])
    {
        if (is_array($controller)) {
            $controller = [
                $this->getContainer()->get($controller[0]),
                $controller[1]
            ];
        }

        return call_user_func_array($controller, array_values($vars));
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
     * @return \Orno\Di\Container
     */
    protected function getContainer()
    {
        return $this->container;
    }
}
