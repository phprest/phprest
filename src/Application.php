<?php namespace Phrest;

use Stack\Builder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Negotiation\FormatNegotiator;
use Hateoas\HateoasBuilder;
use Phrest\Service;
use Phrest\Entity\DebugError;
use Phrest\Entity\Error;

class Application extends \Proton\Application
{
    use Service\Hateoas,
        Service\Serializer;

    public function __construct()
    {
        parent::__construct();

        $this->setErrorHandlers();
        $this->registerServices();
    }

    /**
     * @return void
     */
    protected function setErrorHandlers()
    {
        $this->setExceptionDecorator(function (\Exception $e) {
            throw $e;
        });

        $this->setErrorHandler(function($errNo, $errStr, $errFile, $errLine) {
            throw new \ErrorException($errStr, 0, $errNo, $errFile, $errLine);
        });

        $this->setDefaultExceptionHandler(function(\Exception $exception) {
            $this->getExceptionResponse($exception)->send();
        });
    }

    /**
     * @return void
     */
    protected function registerServices()
    {
        AnnotationRegistry::registerLoader('class_exists');

        $this->container->add('Hateoas', HateoasBuilder::create()->build());

        $this->container->add('Serializer', function($value, Request $request, Response $response) {
            return $this->getHateoasdSerializedResponse($value, $request, $response);
        });
    }

    /**
     * @param mixed $value
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function getHateoasdSerializedResponse($value, Request $request, Response $response)
    {
        $response->setContent(
            $this['Hateoas']->serialize(
                $value,
                $request->attributes->get('_format', 'json')
            )
        );

        $response->headers->set(
            'Content-Type',
            $request->attributes->get('_mime_type', 'application/hal+json')
        );

        return $response;
    }

    /**
     * Run the application
     *
     * @param  Request $request
     *
     * @return void
     */
    public function run(Request $request = null)
    {
        $stack = (new Builder())
            ->push('Negotiation\Stack\Negotiation');

        $app = $stack->resolve($this);

        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        $response = $app->handle($request);
        $response->send();

        $app->terminate($request, $response);
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
    public function setErrorHandler(callable $func) {
        set_error_handler($func);
    }

    /**
     * @param callable $func
     *
     * @return void
     */
    public function setDefaultExceptionHandler(callable $func) {
        set_exception_handler($func);
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
        $request = Request::createFromGlobals();
        $response = new Response();
        $negotiator = new FormatNegotiator();

        $request->attributes->set(
            '_format',
            $negotiator->getBestFormat($request->headers->get('accept')) === 'xml' ? 'xml' : 'json'
        );
        $request->attributes->set(
            '_mime_type',
            'application/json'
        );

        if ($request->attributes->get('_format') === 'xml') {
            $request->attributes->set('_mime_type', 'application/xml');
        }

        $response = $this->serviceSerializer(
            $this['debug'] === false ? new Error($exception) : new DebugError($exception),
            $request,
            $response
        );
        $response->setStatusCode(method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);

        return $response;
    }
}
