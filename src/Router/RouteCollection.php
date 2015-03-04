<?php namespace Phprest\Router;

use League\Route\Strategy\StrategyInterface;

class RouteCollection extends \League\Route\RouteCollection
{
    /**
     * @var array keys: method, route, handler
     */
    protected $routingTable = [];

    /**
     * Add a route to the collection
     *
     * @param  string                                   $method
     * @param  string                                   $route
     * @param  string|\Closure                          $handler
     * @param  \League\Route\Strategy\StrategyInterface $strategy
     * @return \League\Route\RouteCollection
     */
    public function addRoute($method, $route, $handler, StrategyInterface $strategy = null)
    {
        parent::addRoute($method, $route, $handler, $strategy);

        $this->routingTable[] = [
            'method'    => $method,
            'route'     => $route,
            'handler'   => $handler
        ];

        return $this;
    }

    /**
     * @return array keys: method, route, handler
     */
    public function getRoutingTable()
    {
        return $this->routingTable;
    }

    /**
     * Convenience method to convert pre-defined key words in to regex strings
     *
     * @param  string $route
     *
     * @return string
     */
    protected function parseRouteString($route)
    {
        $wildcards = [
            '/{(.+?):number}/'          => '{$1:[0-9]+}',
            '/{(.+?):word}/'            => '{$1:[a-zA-Z]+}',
            '/{(.+?):alphanum_dash}/'   => '{$1:[a-zA-Z0-9-_]+}',
            '/{version:(any)}/'         => '{$1:\d\.\d}'
        ];

        return preg_replace(array_keys($wildcards), array_values($wildcards), $route);
    }
}
