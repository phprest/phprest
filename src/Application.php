<?php namespace Phprest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Phprest\Service;
use Phprest\Entity;

class Application extends \Proton\Application
{
    const CNTRID_DEBUG = 'debug';
    const CNTRID_VENDOR = 'vendor';
    const CNTRID_API_VERSION = 'api-version';
    const CNTRID_API_VERSION_HANDLER = 'api-version-handler';
    const CNTRID_ROUTER = 'router';

    use Service\Hateoas\Getter, Service\Hateoas\Util;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $registeredServiceNames = [];

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->container = $config->getContainer();
        $this->router = $config->getRouter();
        $this->eventEmitter = $config->getEventEmitter();

        AnnotationRegistry::registerLoader('class_exists');

        $this->setErrorHandlers();
        $this->registerService($config->getHateoasService(), $config->getHateoasConfig());

        $this->container->add(self::CNTRID_VENDOR, $config->getVendor());
        $this->container->add(self::CNTRID_API_VERSION, $config->getApiVersion());
        $this->container->add(self::CNTRID_DEBUG, $config->isDebug());
        $this->container->add(self::CNTRID_ROUTER, function() { return $this->router; } );
        $this->container->add(self::CNTRID_API_VERSION_HANDLER, function() use ($config) {
            return $config->getApiVersionHandler();
        });
    }

    /**
     * @param Service\Serviceable $service
     * @param Service\Configurable $config
     *
     * @return void
     *
     * @throws \Exception
     */
    public function registerService(Service\Serviceable $service, Service\Configurable $config)
    {
        if (in_array($config->getServiceName(), $this->registeredServiceNames)) {
            throw new \Exception('Service <' . $config->getServiceName() . '> has been already registered!');
        }

        $service->register($this->container, $config);

        $this->registeredServiceNames[] = $config->getServiceName();
    }

    /**
     * @param string $class Namespaced class name
     *
     * @return void
     */
    public function registerController($class)
    {
        $controller = new $class($this->container);

        $this->container->add($class, function () use ($controller) {
            return $controller;
        });
    }

    /**
     * Add a HEAD route
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function head($route, $action)
    {
        $this->router->addRoute('HEAD', $route, $action);
    }

    /**
     * Add a OPTIONS route
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function options($route, $action)
    {
        $this->router->addRoute('OPTIONS', $route, $action);
    }

    /**
     * @return void
     */
    protected function setErrorHandlers()
    {
        set_error_handler(function($errNo, $errStr, $errFile, $errLine) {
            throw new \ErrorException($errStr, PHP_INT_MAX - 1, $errNo, $errFile, $errLine);
        });

        $exceptionHandler = function(\Exception $exception) {
            $this->getExceptionResponse($exception)->send();
        };

        set_exception_handler($exceptionHandler);

        register_shutdown_function(function() use ($exceptionHandler) {
            if ($error = error_get_last()) {
                call_user_func(
                    $exceptionHandler,
                    new \ErrorException($error['message'], PHP_INT_MAX, $error['type'], $error['file'], $error['line'])
                );
            }
        });

        $this->setExceptionDecorator(function (\Exception $e) {
            throw $e;
        });
    }

    /**
     * Returns with a xml/json response.
     * Default: json.
     *
     * @param \Exception $exception
     *
     * @return Response
     *
     * @throws \Exception
     */
    protected function getExceptionResponse(\Exception $exception)
    {
        if (php_sapi_name() === 'cli') {
            throw $exception;
        }

        $response = new Response();

        try {
            $response = $this->serialize(
                ! $this->config->isDebug() ? new Entity\Error($exception) : new Entity\DebugError($exception),
                Request::createFromGlobals(),
                $response
            );
        } catch (\Exception $e) {
            $response->setContent(
                $this->serviceHateoas()->getSerializer()->serialize(
                    ! $this->config->isDebug() ? new Entity\Error($exception) : new Entity\DebugError($exception),
                    'json'
                )
            );

            $vendor = $this->container->get(self::CNTRID_VENDOR);
            $apiVersion = $this->container->get(self::CNTRID_API_VERSION);

            $response->headers->set('Content-Type', 'application/vnd.' . $vendor . '-v' . $apiVersion . '+json');
        }

        $response->setStatusCode(method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);

        return $response;
    }
}
