<?php namespace Phrest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Phrest\Service\BuiltIn\Serializer\Config as SerializerConfig;
use Phrest\Service\BuiltIn\Hateoas\Config as HateoasConfig;
use Phrest\Service;
use Phrest\Router\Strategy;
use Phrest\Entity;
use Phrest\Negotiate;

class Application extends \Proton\Application
{
    use Service\BuiltIn\Serializer\Getter, Service\BuiltIn\Hateoas\Getter;
    use Negotiate\Serializer;

    /**
     * @var callable
     */
    protected $exceptionHandler;

    /**
     * @var array
     */
    protected $registeredServiceNames = [];

    /**
     * @param SerializerConfig $serializerConfig
     * @param HateoasConfig $hateoasConfig
     * @param Strategy $routerStrategy
     */
    public function __construct(SerializerConfig $serializerConfig = null,
                                HateoasConfig $hateoasConfig = null,
                                Strategy $routerStrategy = null)
    {
        parent::__construct();

        $this->setErrorHandlers();
        $this->registerBuiltInServices($serializerConfig, $hateoasConfig);
        $this->setRouterStrategy($routerStrategy);
    }

    /**
     * @return void
     */
    protected function setErrorHandlers()
    {
        $this->setExceptionDecorator(function (\Exception $e) {
            throw $e;
        });

        set_error_handler(function($errNo, $errStr, $errFile, $errLine) {
            throw new \ErrorException($errStr, 0, $errNo, $errFile, $errLine);
        });

        $this->setDefaultExceptionHandler(function(\Exception $exception) {
            $this->getExceptionResponse($exception)->send();
        });

        register_shutdown_function(function() {
            if ($error = error_get_last()) {
                call_user_func(
                    $this->exceptionHandler,
                    new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
                );
            }
        });
    }

    /**
     * @param SerializerConfig $serializerConfig
     * @param HateoasConfig $hateoasConfig
     *
     * @return void
     */
    protected function registerBuiltInServices(SerializerConfig $serializerConfig = null,
                                               HateoasConfig $hateoasConfig = null)
    {
        AnnotationRegistry::registerLoader('class_exists');

        if (is_null($serializerConfig)) {
            $serializerConfig = new SerializerConfig($this['debug']);
        }
        $this->registerService(new Service\BuiltIn\Serializer\Service(), $serializerConfig);

        if (is_null($hateoasConfig)) {
            $hateoasConfig = new HateoasConfig($this['debug']);
        }
        $this->registerService(new Service\BuiltIn\Hateoas\Service(), $hateoasConfig);
    }

    /**
     * @param Strategy $routerStrategy
     *
     * @return void
     */
    protected function setRouterStrategy(Strategy $routerStrategy = null)
    {
        if (is_null($routerStrategy)) {
            $routerStrategy = new Strategy($this->container);
        }
        $this->router->setStrategy($routerStrategy);
    }

    /**
     * @param Service\Contract\Serviceable $service
     * @param Service\Contract\Configurable $config
     *
     * @return void
     *
     * @throws \Exception
     */
    public function registerService(Service\Contract\Serviceable $service, Service\Contract\Configurable $config)
    {
        if (in_array($config->getServiceName(), $this->registeredServiceNames)) {
            throw new \Exception('Service <' . $config->getServiceName() . '> has been already registered!');
        }

        $service->register($this->container, $config);

        $this->registeredServiceNames[] = $config->getServiceName();
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
     * @param callable $func
     *
     * @return void
     */
    public function setDefaultExceptionHandler(callable $func) {
        set_exception_handler($func);

        $this->exceptionHandler = $func;
    }

    /**
     * Returns with a xml/json response.
     * Default: json.
     *
     * @param \Exception $exception
     *
     * @return Response
     */
    protected function getExceptionResponse(\Exception $exception)
    {
        $response = new Response();

        try {
            $response = $this->serialize(
                $this['debug'] === false ? new Entity\Error($exception) : new Entity\DebugError($exception),
                Request::createFromGlobals(),
                $response
            );
        } catch (\Exception $e) {
            $response->setContent(
                $this->serviceSerializer()->serialize(
                    $this['debug'] === false ? new Entity\Error($exception) : new Entity\DebugError($exception),
                    'json'
                )
            );
            $response->headers->set('Content-Type', Negotiate\Mime::JSON);
        }

        $response->setStatusCode(method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);

        return $response;
    }
}
