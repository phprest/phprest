<?php

namespace Phprest\Test\Service\Hateoas;

use Phprest\Application;
use League\Container\Container;
use Phprest\Exception\NotAcceptable;
use Phprest\Exception\UnsupportedMediaType;
use Phprest\Service\Hateoas\Config;
use Phprest\Service\Hateoas\Getter;
use Phprest\Service\Hateoas\Service;
use Phprest\Service\Hateoas\Util;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Phprest\Stub\Entity\Sample;

class UtilTest extends TestCase
{
    use Getter;
    use Util;

    private Container $container;

    public static function setUpBeforeClass(): void
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    public function setUp(): void
    {
        $this->container = new Container();

        $service = new Service();
        $service->register($this->container, new Config(true));
    }

    public function testJsonSerialize(): void
    {
        $request = $this->setRequestParameters('phprest', '2.4', 'application/json');

        $result = $this->serialize(['a' => 1, 'b' => 2], $request, new Response());

        $this->assertEquals('{"a":1,"b":2}', $result->getContent());
    }

    public function testXmlSerialize(): void
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
            ,
            $result->getContent()
        );
    }

    public function testDefaultSerialize(): void
    {
        $request = $this->setRequestParameters('phprest', '2.4', '*/*');

        $result = $this->serialize(['a' => 1, 'b' => 2], $request, new Response());

        $this->assertEquals('{"a":1,"b":2}', $result->getContent());
    }

    public function testNotAcceptableSerialize(): void
    {
        $this->expectException(NotAcceptable::class);

        $request = $this->setRequestParameters('phprest', '2.4', 'yaml');

        $this->serialize(['a' => 1, 'b' => 2], $request, new Response());
    }

    public function testJsonDeserialize(): void
    {
        $this->container->add(Application::CONTAINER_ID_VENDOR, 'phprest');
        $this->container->add(Application::CONTAINER_ID_API_VERSION, '3.2');

        $request = new Request([], [], [], [], [], [], '{"a":1,"b":2}');
        $request->headers->set('Content-Type', 'application/json');

        $sample = $this->deserialize(Sample::class, $request);

        $this->assertInstanceOf(Sample::class, $sample);
        $this->assertEquals(1, $sample->a);
        $this->assertEquals(2, $sample->b);
    }

    public function testJsonDeserializeWithUnsopportedFormat(): void
    {
        $this->expectException(UnsupportedMediaType::class);

        $this->container->add(Application::CONTAINER_ID_VENDOR, 'phprest');
        $this->container->add(Application::CONTAINER_ID_API_VERSION, '3.2');

        $request = new Request([], [], [], [], [], [], '{"a":1,"b":2}');
        $request->headers->set('Content-Type', 'application/yaml');

        $this->deserialize(Sample::class, $request);
    }

    /**
     * @param string|integer $apiVersion
     * @param string $acceptHeader
     *
     * @return Request
     */
    protected function setRequestParameters(string $vendor, $apiVersion, $acceptHeader): Request
    {
        $this->container->add(Application::CONTAINER_ID_VENDOR, $vendor);
        $this->container->add(Application::CONTAINER_ID_API_VERSION, $apiVersion);

        (new Service())->
        register($this->container, new Config(true));

        $request = new Request();
        $request->headers->set('Accept', $acceptHeader);

        $this->container->add('Orno\Http\Request', $request);

        return $request;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
