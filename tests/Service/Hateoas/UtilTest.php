<?php namespace Phprest\Service\Hateoas;

use Phprest\Application;
use Orno\Di\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    use Getter;
    use Util;

    /**
     * @var Container
     */
    private $container;

    public static function setUpBeforeClass()
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    public function setUp()
    {
        $this->container = new Container();

        $service = new Service();
        $service->register($this->container, new Config());
    }

    public function testJsonSerialize()
    {
        $request = $this->setRequestParameters('phprest', '2.4', 'application/json');

        $result = $this->serialize(['a' => 1, 'b' => 2], $request, new Response());

        $this->assertEquals('{"a":1,"b":2}', $result->getContent());
    }

    public function testXmlSerialize()
    {
        $request = $this->setRequestParameters('phprest', '2.4', 'application/xml');

        $result = $this->serialize(['a' => 1, 'b' => 2], $request, new Response());

        $this->assertEquals(
<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry>1</entry>
  <entry>2</entry>
</result>

EOD
            , $result->getContent());
    }

    public function testDefaultSerialize()
    {
        $request = $this->setRequestParameters('phprest', '2.4', '*/*');

        $result = $this->serialize(['a' => 1, 'b' => 2], $request, new Response());

        $this->assertEquals('{"a":1,"b":2}', $result->getContent());
    }

    /**
     * @expectedException \Phprest\Exception\NotAcceptable
     */
    public function testNotAcceptableSerialize()
    {
        $request = $this->setRequestParameters('phprest', '2.4', 'yaml');

        $this->serialize(['a' => 1, 'b' => 2], $request, new Response());
    }

    public function testJsonDeserialize()
    {
        $this->container->add(Application::CNTRID_VENDOR, 'phprest');
        $this->container->add(Application::CNTRID_API_VERSION, '3.2');

        $request = new Request([],[],[],[],[],[], '{"a":1,"b":2}');
        $request->headers->set('Content-Type', 'application/json', true);

        $sample = $this->deserialize('Phprest\Stub\Entity\Sample', $request);

        $this->assertInstanceOf('Phprest\Stub\Entity\Sample', $sample);
        $this->assertEquals(1, $sample->a);
        $this->assertEquals(2, $sample->b);
    }

    /**
     * @expectedException \Phprest\Exception\UnsupportedMediaType
     */
    public function testJsonDeserializeWithUnsopportedFormat()
    {
        $this->container->add(Application::CNTRID_VENDOR, 'phprest');
        $this->container->add(Application::CNTRID_API_VERSION, '3.2');

        $request = new Request([],[],[],[],[],[], '{"a":1,"b":2}');
        $request->headers->set('Content-Type', 'application/yaml', true);

        $sample = $this->deserialize('Phprest\Stub\Entity\Sample', $request);
    }

    /**
     * @param string $vendor
     * @param string|integer $apiVersion
     * @param string $acceptHeader
     *
     * @return Request
     */
    protected function setRequestParameters($vendor, $apiVersion, $acceptHeader)
    {
        $this->container->add(Application::CNTRID_VENDOR, $vendor);
        $this->container->add(Application::CNTRID_API_VERSION, $apiVersion);

        (new Service())->
        register($this->container, new Config(true));

        $request = new Request();
        $request->headers->set('Accept', $acceptHeader, true);

        $this->container->add('Orno\Http\Request', $request);

        return $request;
    }

    protected function getContainer()
    {
        return $this->container;
    }
}