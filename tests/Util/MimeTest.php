<?php namespace Phprest\Util;

use Phprest\Application;
use League\Container\Container;
use Phprest\Util\DataStructure\MimeProcessResult;
use PHPUnit\Framework\TestCase;

class MimeTest extends TestCase
{
    use Mime;

    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function testSimpleAcceptJsonMime(): void
    {
        $this->setVendorInContainer('phprest-test');
        $this->setApiVersionInContainer('1.5');

        $result = $this->processMime('application/json');

        $this->assertInstanceOf(MimeProcessResult::class, $result);

        $this->assertEquals('application/vnd.phprest-test-v1.5+json', $result->mime);
        $this->assertEquals('phprest-test', $result->vendor);
        $this->assertEquals('1.5', $result->apiVersion);
        $this->assertEquals('json', $result->format);
    }

    public function testSimpleAcceptXmlMime(): void
    {
        $this->setVendorInContainer('phprest-test');
        $this->setApiVersionInContainer('1.5');

        $result = $this->processMime('application/xml');

        $this->assertInstanceOf(MimeProcessResult::class, $result);

        $this->assertEquals('application/vnd.phprest-test-v1.5+xml', $result->mime);
        $this->assertEquals('xml', $result->format);
    }

    public function testComplexVersionFormatAcceptJsonMime(): void
    {
        $this->setVendorInContainer('phprest-test');
        $this->setApiVersionInContainer('2.7');

        $result = $this->processMime('application/vnd.phprest-test-v2.7+json');

        $this->assertInstanceOf(MimeProcessResult::class, $result);

        $this->assertEquals('application/vnd.phprest-test-v2.7+json', $result->mime);
        $this->assertEquals('2.7', $result->apiVersion);
        $this->assertEquals('json', $result->format);
    }

    public function testComplexVersionFormatAcceptXmlMime(): void
    {
        $this->setVendorInContainer('phprest-test');
        $this->setApiVersionInContainer(3);

        $result = $this->processMime('application/vnd.phprest-test+xml; version=3');

        $this->assertInstanceOf(MimeProcessResult::class, $result);

        $this->assertEquals('application/vnd.phprest-test+xml; version=3', $result->mime);
        $this->assertEquals(3, $result->apiVersion);
        $this->assertEquals('xml', $result->format);
    }

    /**
     * @param string $vendor
     */
    protected function setVendorInContainer($vendor): void
    {
        $this->container->add(Application::CONTAINER_ID_VENDOR, $vendor);
    }

    /**
     * @param string|integer $apiVersion
     */
    protected function setApiVersionInContainer($apiVersion): void
    {
        $this->container->add(Application::CONTAINER_ID_API_VERSION, $apiVersion);
    }

    /**
     * Returns the DI container
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }
}
