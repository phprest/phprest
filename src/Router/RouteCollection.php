<?php namespace Phprest\Router;

class RouteCollection extends \Orno\Route\RouteCollection
{
    /**
     * @var array keys: method, route, handler
     */
    protected $routingTable = [];

    /**
     * Add a route to the collection
     *
     * @param  string          $method
     * @param  string          $route
     * @param  string|\Closure $handler
     * @param  integer         $strategy
     * @return \Orno\Route\RouteCollection
     */
    public function addRoute($method, $route, $handler, $strategy = self::REQUEST_RESPONSE_STRATEGY)
    {
        parent::addRoute($method, $route, $handler, $strategy);

        $this->routingTable[] = [
            'method'    => $method,
            'route'     => $route,
            'handler'   => $handler
        ];
    }

    /**
     * @return array keys: method, route, handler
     */
    public function getRoutingTable()
    {
        return $this->routingTable;
    }
}
