<?php

namespace Phprest\Test\Util;

use Phprest\Application;
use Phprest\Stub\Controller\Routed as RoutedController;
use Phprest\Router\RouteCollection;
use League\Container\Container;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    private RoutedController $controller;
    private RouteCollection $router;

    public static function setUpBeforeClass(): void
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    public function setUp(): void
    {
        $this->router = new RouteCollection();

        $container = new Container();
        $container->add(Application::CONTAINER_ID_ROUTER, $this->router);

        $this->controller = new RoutedController($container);
    }

    public function testRoutingTable(): void
    {
        $routingTable = $this->router->getRoutingTable();

        $this->assertCount(2, $routingTable);

        $this->assertEquals('GET', $routingTable[0]['method']);
        $this->assertEquals('\Phprest\Stub\Controller\Routed::getFoo', $routingTable[0]['handler']);
        $this->assertEquals('/{version:(?:1\.[2-9])|(?:2\.[0-8])}/foos/{id}', $routingTable[0]['route']);

        $this->assertEquals('POST', $routingTable[1]['method']);
        $this->assertEquals('\Phprest\Stub\Controller\Routed::postBar', $routingTable[1]['handler']);
        $this->assertEquals('/{version:(?:0\.[5-7])}/bars', $routingTable[1]['route']);
    }
}
