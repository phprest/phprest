<?php namespace Phprest\ErrorHandler\Formatter;

use Exception;
use LogicException;
use Phprest\Application;
use Phprest\Config;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Phprest\Exception\BadRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JsonXmlTest extends TestCase
{
    /**
     * @var Config
     */
    protected $config;

    public function setUp()
    {
        $this->config = new Config('phprest', 1, true);
        $this->setContainerElements($this->config);
    }

    public function testFormatWithSimpleException(): void
    {
        $jsonXmlFormatter = new JsonXml($this->config);

        $this->assertContains(
            '"code":9,"message":"test","details":[]',
            $jsonXmlFormatter->format(new LogicException('test', 9))
        );
    }

    public function testFormatWithDetailedException(): void
    {
        $jsonXmlFormatter = new JsonXml($this->config);

        $this->assertContains(
            '"code":11,"message":"Bad Request","details":[1,2,3,["a","b"]]',
            $jsonXmlFormatter->format(new BadRequest(11, [1,2,3,['a','b']]))
        );
    }

    public function testFormatWithNotAcceptable(): void
    {
        $request = Request::createFromGlobals();
        $request->headers->set('Accept', 'yaml');

        $jsonXmlFormatter = new JsonXml($this->config, $request);

        $this->assertContains(
            '"code":0,"message":"Not Acceptable","details":["yaml is not supported"]',
            $jsonXmlFormatter->format(new Exception())
        );
    }

    /**
     * @param Config $config
     */
    protected function setContainerElements(Config $config): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        $config->getHateoasService()->register(
            $config->getContainer(),
            $config->getHateoasConfig()
        );

        $config->getContainer()->add(Application::CONTAINER_ID_VENDOR, $config->getVendor());
        $config->getContainer()->add(Application::CONTAINER_ID_API_VERSION, $config->getApiVersion());
        $config->getContainer()->add(Application::CONTAINER_ID_DEBUG, $config->isDebug());
    }
}
