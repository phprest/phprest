<?php namespace Phprest;

use Orno\Di\Container;
use Orno\Route\RouteCollection;
use Phprest\Router\RouteCollection as PhprestRouteCollection;
use Phprest\Router\Strategy as RouterStrategy;
use League\Event\Emitter as EventEmitter;
use Phprest\Service\Hateoas\Service as HateoasService;
use Phprest\Service\Hateoas\Config as HateoasConfig;

class Config
{
    /**
     * @var string
     */
    protected $vendor;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var boolean
     */
    protected $debug;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var RouteCollection
     */
    protected $router;

    /**
     * @var EventEmitter
     */
    protected $eventEmitter;

    /**
     * @var HateoasConfig
     */
    protected $hateoasConfig;

    /**
     * @var HateoasService
     */
    protected $hateoasService;

    /**
     * @var callable
     */
    protected $apiVersionHandler;

    /**
     * @param string $vendor
     * @param string $apiVersion
     */
    public function __construct($vendor, $apiVersion)
    {
        $this->vendor = $vendor;
        $this->apiVersion = $apiVersion;

        $this->setDebug(false);
        $this->setContainer(new Container());
        $this->setRouter(new PhprestRouteCollection());
        $this->setEventEmitter(new EventEmitter());
        $this->setHateoasService(new HateoasService());
        $this->setHateoasConfig(new HateoasConfig($this->debug));

        $this->router->setStrategy(new RouterStrategy($this->container));
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param RouteCollection $router
     */
    public function setRouter(RouteCollection $router)
    {
        $this->router = $router;
    }

    /**
     * @return RouteCollection
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param EventEmitter $eventEmitter
     */
    public function setEventEmitter(EventEmitter $eventEmitter)
    {
        $this->eventEmitter = $eventEmitter;
    }

    /**
     * @return EventEmitter
     */
    public function getEventEmitter()
    {
        return $this->eventEmitter;
    }

    /**
     * @param HateoasService $service
     */
    public function setHateoasService(HateoasService $service)
    {
        $this->hateoasService = $service;
    }

    /**
     * @return HateoasService
     */
    public function getHateoasService()
    {
        return $this->hateoasService;
    }

    /**
     * @param HateoasConfig $config
     */
    public function setHateoasConfig(HateoasConfig $config)
    {
        $this->hateoasConfig = $config;
    }

    /**
     * @return HateoasConfig
     */
    public function getHateoasConfig()
    {
        return $this->hateoasConfig;
    }

    /**
     * @param callable $func
     */
    public function setApiVersionHandler(callable $func)
    {
        $this->apiVersionHandler = $func;
    }

    /**
     * @return callable
     */
    public function getApiVersionHandler()
    {
        return $this->apiVersionHandler;
    }
}
