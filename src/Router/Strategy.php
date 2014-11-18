<?php namespace Phrest\Router;

use Orno\Route\CustomStrategyInterface;
use Phrest\HttpFoundation\Response;
use Orno\Di\Container;
use Phrest\Negotiate;

class Strategy implements CustomStrategyInterface
{
    use Negotiate\Serializer;

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
     * @param  string|array|\Closure $controller
     * @param  array                 $vars - named wildcard segments of the matched route
     * @return mixed
     */
    public function dispatch($controller, array $vars)
    {
        $handler = $controller;

        // figure out what the controller is
        if (($handler instanceof \Closure) || (is_string($handler) && is_callable($handler))) {
            $controller = $handler;
        }

        if (is_string($handler) && strpos($handler, '::') !== false) {
            $controller = explode('::', $handler);
        }

        // if controller method wasn't specified, throw exception.
        if (! $controller){
            throw new \RuntimeException('A class method must be provided as a controller. ClassName::methodName');
        }

        $request = $this->container->get('Orno\Http\Request');

        $response = $this->invokeController($controller, array_merge([$request], $vars));

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
     * Invoke a controller action
     *
     * @param  string|\Closure $controller
     * @param  array           $vars
     * @return \Orno\Http\ResponseInterface
     */
    public function invokeController($controller, array $vars = [])
    {
        if (is_array($controller)) {
            $controller = [
                $this->container->get($controller[0]),
                $controller[1]
            ];
        }

        return call_user_func_array($controller, array_values($vars));
    }

    /**
     * @return \Hateoas\Hateoas
     */
    public function serviceHateoas()
    {
        return $this->container->get('Hateoas');
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    public function serviceSerializer()
    {
        return $this->container->get('Serializer');
    }
}
