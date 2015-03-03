<?php namespace Phprest;

use Phprest\Router\RouteCollection;
use Phprest\Router\Strategy as RouterStrategy;
use Phprest\Service\Hateoas\Config as HateoasConfig;
use Phprest\Service\Hateoas\Service as HateoasService;
use Phprest\Service\Logger\Config as LoggerConfig;
use Phprest\Service\Logger\Service as LoggerService;
use League\Event\Emitter as EventEmitter;
use League\Route\Strategy\StrategyInterface;
use League\Container\Container;

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
     * @var LoggerConfig
     */
    protected $loggerConfig;

    /**
     * @var LoggerService
     */
    protected $loggerService;

    /**
     * @param string $vendor
     * @param string $apiVersion
     * @param boolean $debug
     */
    public function __construct($vendor, $apiVersion, $debug = false)
    {
        if ( ! preg_match('#^' . Application::API_VERSION_REG_EXP . '$#', (string)$apiVersion)) {
            throw new \InvalidArgumentException('Api version is not valid');
        }

        $this->vendor = $vendor;
        $this->apiVersion = $apiVersion;
        $this->debug = $debug;

        $this->setContainer(new Container());
        $this->setRouter(new RouteCollection());
        $this->setEventEmitter(new EventEmitter());
        $this->setHateoasConfig(new HateoasConfig($debug));
        $this->setHateoasService(new HateoasService());

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
     * @param StrategyInterface $strategy
     */
    public function setRouterStrategy(StrategyInterface $strategy)
    {
        $this->router->setStrategy($strategy);
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
     * @param LoggerConfig $config
     */
    public function setLoggerConfig(LoggerConfig $config)
    {
        $this->loggerConfig = $config;
    }

    /**
     * @return LoggerConfig
     */
    public function getLoggerConfig()
    {
        return $this->loggerConfig;
    }

    /**
     * @param LoggerService $service
     */
    public function setLoggerService(LoggerService $service)
    {
        $this->loggerService = $service;
    }

    /**
     * @return LoggerService
     */
    public function getLoggerService()
    {
        return $this->loggerService;
    }
}
