<?php namespace Phrest;

use Stack\Builder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Negotiation\FormatNegotiator;
use Hateoas\HateoasBuilder;
use Phrest\Service;

class Application extends \Proton\Application
{
    use Service\Hateoas,
        Service\Serializer;

    public function __construct()
    {
        $this->setErrorHandler(function($errNo, $errStr, $errFile, $errLine) {
            throw new \ErrorException($errStr, 0, $errNo, $errFile, $errLine);
        });

        $this->setDefaultExceptionHandler(function(\Exception $exception) {
            $this->getExceptionResponse($exception)->send();
        });

        parent::__construct();

        $this->setExceptionDecorator(function (\Exception $e) {
            throw $e;
        });

        AnnotationRegistry::registerLoader('class_exists');

        $this->container->add('Hateoas', HateoasBuilder::create()->build());

        $this->container->add('Serializer', function($value, Request $request, Response $response) {
            return $this->getHateoasdSerializedResponse($value, $request, $response);
        });
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
     * Returns a xml/json response with message and code properties.
     * Default: json.
     *
     * @param \Exception $exception
     *
     * @return Response
     */
    protected function getExceptionResponse(\Exception $exception)
    {
        $response = new Response();

        $negotiator   = new FormatNegotiator();
        $acceptHeader = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'application/json';

        $format = $negotiator->getBestFormat($acceptHeader);
        $formatMime = $negotiator->getBest($acceptHeader)->getValue();

        if ($format === 'xml') {
            $response->setContent($this->getErrorXml($exception)->asXML());
        } else {
            $formatMime = 'application/json';

            $response->setContent($this->getErrorJson($exception));
        }

        $response->setStatusCode(method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);
        $response->headers->add(['Content-Type' => $formatMime]);

        return $response;
    }

    /**
     * @param \Exception $exception
     *
     * @return \SimpleXMLElement
     */
    protected function getErrorXml(\Exception $exception)
    {
        $xml = new \SimpleXMLElement('<result/>');

        $xml->addChild('message', '<![CDATA[' . $exception->getMessage() . ']]>');
        $xml->addChild('code', $exception->getCode());

        if ($this['debug'] === true) {
            $debug = $xml->addChild('debug');

            $debug->addChild('file', $exception->getFile());
            $debug->addChild('line', $exception->getLine());
            $debug->addChild('trace', '<![CDATA[' . $exception->getTraceAsString() . ']]>');
        }

        return $xml;
    }

    /**
     * @param \Exception $exception
     *
     * @return string
     */
    protected function getErrorJson(\Exception $exception)
    {
        $return = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode()
        ];

        if ($this['debug'] === true) {
            $return['debug']['file'] = $exception->getFile();
            $return['debug']['line'] = $exception->getLine();
            $return['debug']['trace'] = explode(PHP_EOL, $exception->getTraceAsString());
        }

        return json_encode($return);
    }
}
