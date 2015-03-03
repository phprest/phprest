<?php namespace Phprest\Util;

use Phprest\Application;
use League\Container\Container;

class MimeTest extends \PHPUnit_Framework_TestCase
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

    public function testSimpleAcceptJsonMime()
    {
        $this->setVendorInContainer('phprest-test');
        $this->setApiVersionInContainer('1.5');

        $result = $this->processMime('application/json');

        $this->assertInstanceOf('Phprest\Util\DataStructure\MimeProcessResult', $result);

        $this->assertEquals('application/vnd.phprest-test-v1.5+json', $result->mime);
        $this->assertEquals('phprest-test', $result->vendor);
        $this->assertEquals('1.5', $result->apiVersion);
        $this->assertEquals('json', $result->format);
    }

    public function testSimpleAcceptXmlMime()
    {
        $this->setVendorInContainer('phprest-test');
        $this->setApiVersionInContainer('1.5');

        $result = $this->processMime('application/xml');

        $this->assertInstanceOf('Phprest\Util\DataStructure\MimeProcessResult', $result);

        $this->assertEquals('application/vnd.phprest-test-v1.5+xml', $result->mime);
        $this->assertEquals('xml', $result->format);
    }

    public function testComplexVersionFormatAcceptJsonMime()
    {
        $this->setVendorInContainer('phprest-test');
        $this->setApiVersionInContainer('2.7');

        $result = $this->processMime('application/vnd.phprest-test-v2.7+json');

        $this->assertInstanceOf('Phprest\Util\DataStructure\MimeProcessResult', $result);

        $this->assertEquals('application/vnd.phprest-test-v2.7+json', $result->mime);
        $this->assertEquals('2.7', $result->apiVersion);
        $this->assertEquals('json', $result->format);
    }

    public function testComplexVersionFormatAcceptXmlMime()
    {
        $this->setVendorInContainer('phprest-test');
        $this->setApiVersionInContainer(3);

        $result = $this->processMime('application/vnd.phprest-test+xml; version=3');

        $this->assertInstanceOf('Phprest\Util\DataStructure\MimeProcessResult', $result);

        $this->assertEquals('application/vnd.phprest-test+xml; version=3', $result->mime);
        $this->assertEquals(3, $result->apiVersion);
        $this->assertEquals('xml', $result->format);
    }

    /**
     * @param string $vendor
     */
    protected function setVendorInContainer($vendor)
    {
        $this->container->add(Application::CNTRID_VENDOR, $vendor);
    }

    /**
     * @param string|integer $apiVersion
     */
    protected function setApiVersionInContainer($apiVersion)
    {
        $this->container->add(Application::CNTRID_API_VERSION, $apiVersion);
    }

    /**
     * Returns the DI container
     *
     * @return \League\Container\Container
     */
    protected function getContainer()
    {
        return $this->container;
    }
}
