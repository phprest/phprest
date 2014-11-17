<?php namespace Phrest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Phrest\Negotiate;
use Hateoas\Hateoas;
use JMS\Serializer\Serializer;
use Phrest\Router\Strategy;
use Phrest\Entity;

class Application extends \Proton\Application
{
    use Negotiate\Serializer;

    /**
     * @var callable
     */
    protected $exceptionHandler;

    /**
     * @param Serializer $serializer
     * @param Hateoas $hateoas
     * @param Strategy $strategy
     */
    public function __construct(Serializer $serializer = null, Hateoas $hateoas = null, Strategy $strategy = null)
    {
        parent::__construct();

        $this->setErrorHandlers();
        $this->registerServices($serializer, $hateoas);
        $this->setStrategy($strategy);
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
     * @return void
     */
    protected function registerServices(Serializer $serializer = null, Hateoas $hateoas = null)
    {
        AnnotationRegistry::registerLoader('class_exists');

        if (is_null($serializer)) {
            $serializer = \JMS\Serializer\SerializerBuilder::create()->setDebug($this['debug'])->build();
        }
        $this->container->add('Serializer', $serializer);

        if (is_null($hateoas)) {
            $hateoas = \Hateoas\HateoasBuilder::create()->setDebug($this['debug'])->build();
        }
        $this->container->add('Hateoas', $hateoas);
    }

    /**
     * @param Strategy $strategy
     */
    protected function setStrategy(Strategy $strategy = null)
    {
        if (is_null($strategy)) {
            $strategy = new Strategy($this->container);
        }
        $this->router->setStrategy($strategy);
    }

    /**
     * @return Hateoas
     */
    public function serviceHateoas()
    {
        return $this->container->get('Hateoas');
    }

    /**
     * @return Serializer
     */
    public function serviceSerializer()
    {
        return $this->container->get('Serializer');
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
