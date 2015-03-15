<?php namespace Phprest\Util;

use Phprest\Application;
use Phprest\Annotation\Route;
use League\Container\Container;
use League\Route\RouteCollection;
use Doctrine\Common\Annotations\AnnotationReader;

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
        $router = $this->getContainer()->get(Application::CNTRID_ROUTER);

        /** @var \ReflectionMethod $method */
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $this->registerRoute(
                $router,
                $class,
                $method,
                $reader->getMethodAnnotation($method, '\Phprest\Annotation\Route')
            );
        }
    }

    /**
     * @param RouteCollection $router
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @param mixed $docblock
     */
    protected function registerRoute(
        RouteCollection $router,
        \ReflectionClass $class,
        \ReflectionMethod $method,
        $docblock
    ) {
        if ($docblock instanceof Route) {
            $this->addVersionToRoute($docblock);

            $router->addRoute(
                $docblock->method,
                $docblock->path,
                '\\' . $class->getName() . '::' . $method->getName()
            );
        }
    }

    /**
     * @param Route $docblock
     */
    protected function addVersionToRoute(Route $docblock)
    {
        if (! is_null($docblock->version) and $docblock->path[0] === '/') {
            $docblock->path = '/' . $docblock->version . $docblock->path;
        } elseif (! is_null($docblock->version) and $docblock->path[0] !== '/') {
            $docblock->path = '/' . $docblock->version . '/' . $docblock->path;
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
