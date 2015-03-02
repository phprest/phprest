<?php namespace Phprest\Util;

use Phprest\Application;
use Phprest\Stub\Controller\Routed as RoutedController;
use Orno\Di\Container;
use Phprest\Router\RouteCollection;
use Doctrine\Common\Annotations\AnnotationRegistry;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoutedController
     */
    private $controller;

    /**
     * @var RouteCollection
     */
    private $router;

    public static function setUpBeforeClass()
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    public function setUp()
    {
        $this->router = new RouteCollection();

        $container = new Container();
        $container->add(Application::CNTRID_ROUTER, $this->router);

        $this->controller = new RoutedController($container);
    }

    public function testRoutingTable()
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
