<?php

namespace Phprest\Test\Router;

use Phprest\Router\RouteCollection;
use PHPUnit\Framework\TestCase;

class RouteCollectionTest extends TestCase
{
    public function testGetRoutingTable(): void
    {
        $routeCollection = new RouteCollection();

        $routeCollection->addRoute('GET', '/temperatures', function () {
        });
        $routeCollection->addRoute('GET', '/temperatures/1', function () {
        });
        $routeCollection->addRoute('POST', '/camera', function () {
        });

        $this->assertCount(3, $routeCollection->getRoutingTable());
        $this->assertEquals('/temperatures', $routeCollection->getRoutingTable()[0]['route']);
        $this->assertEquals('/temperatures/1', $routeCollection->getRoutingTable()[1]['route']);
        $this->assertEquals('/camera', $routeCollection->getRoutingTable()[2]['route']);
    }
}
