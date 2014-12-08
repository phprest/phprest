<?php namespace Phrest\Util;

use Orno\Di\Container;
use Orno\Route\RouteCollection;
use Doctrine\Common\Annotations\AnnotationReader;
use Phrest\Annotation\Route;

abstract class Controller
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     * @param boolean $registerRoutes
     */
    public function __construct(Container $container, $registerRoutes = true)
    {
        $this->container = $container;

        if ($registerRoutes) {
            $this->registerRoutes();
        }
    }

    /**
     * @return void
     */
    protected function registerRoutes()
    {
        $reader = new AnnotationReader();
        $class = new \ReflectionClass($this);
        /** @var RouteCollection $router */
        $router = $this->container->get('router');

        /** @var \ReflectionMethod $method */
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {

            $docblock = $reader->getMethodAnnotation($method, '\Phrest\Annotation\Route');

            if ($docblock instanceof Route) {
                $router->addRoute(
                    $docblock->method,
                    $docblock->path, '\\' . $class->getName() . '::' . $method->getName()
                );
            }
        }
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }
}
