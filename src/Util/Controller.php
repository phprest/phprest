<?php

namespace Phprest\Util;

use Doctrine\Common\Annotations\AnnotationReader;
use League\Container\ContainerInterface;
use League\Route\RouteCollection;
use Phprest\Annotation\Route;
use Phprest\Application;
use ReflectionClass;
use ReflectionMethod;

abstract class Controller
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     * @param bool $registerRoutes
     */
    public function __construct(ContainerInterface $container, $registerRoutes = true)
    {
        $this->container = $container;

        if ($registerRoutes) {
            $this->registerRoutes();
        }
    }

    protected function registerRoutes(): void
    {
        $reader = new AnnotationReader();
        $class  = new ReflectionClass($this);
        /** @var RouteCollection $router */
        $router = $this->getContainer()->get(Application::CONTAINER_ID_ROUTER);

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $this->registerRoute(
                $router,
                $class,
                $method,
                $reader->getMethodAnnotation($method, Route::class)
            );
        }
    }

    /**
     * @param RouteCollection $router
     * @param ReflectionClass $class
     * @param ReflectionMethod $method
     * @param mixed $docblock
     */
    protected function registerRoute(
        RouteCollection $router,
        ReflectionClass $class,
        ReflectionMethod $method,
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

    protected function addVersionToRoute(Route $docblock): void
    {
        if (! is_null($docblock->version) && $docblock->path[0] === '/') {
            $docblock->path = '/' . $docblock->version . $docblock->path;
        } elseif (! is_null($docblock->version) && $docblock->path[0] !== '/') {
            $docblock->path = '/' . $docblock->version . '/' . $docblock->path;
        }
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }
}
