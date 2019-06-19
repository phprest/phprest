<?php

namespace Phprest\Router;

use Closure;
use FastRoute\DataGenerator;
use FastRoute\RouteParser;
use League\Container\ContainerInterface;
use League\Route\Strategy\StrategyInterface;

class RouteCollection extends \League\Route\RouteCollection
{
    /**
     * @var array keys: method, route, handler
     */
    protected $routingTable = [];

    /**
     * @param ContainerInterface $container
     * @param RouteParser $parser
     * @param DataGenerator $generator
     */
    public function __construct(
        ContainerInterface $container = null,
        RouteParser        $parser = null,
        DataGenerator      $generator = null
    ) {
        parent::__construct($container, $parser, $generator);

        $this->addPatternMatcher('any', '\d\.\d');
    }

    /**
     * Add a route to the collection.
     *
     * @param  string                                   $method
     * @param  string                                   $route
     * @param  string|Closure                          $handler
     * @param StrategyInterface $strategy
     *
     * @return RouteCollection
     */
    public function addRoute($method, $route, $handler, StrategyInterface $strategy = null): RouteCollection
    {
        parent::addRoute($method, $route, $handler, $strategy);

        $this->routingTable[] = [
            'method'    => $method,
            'route'     => $route,
            'handler'   => $handler,
        ];

        return $this;
    }

    /**
     * @return array keys: method, route, handler
     */
    public function getRoutingTable(): array
    {
        return $this->routingTable;
    }
}
