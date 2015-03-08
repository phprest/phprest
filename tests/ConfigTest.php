<?php namespace Phprest;

use Phprest\Service\Logger\Config as LoggerConfig;
use Phprest\Service\Logger\Service as LoggerService;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider correctApiVersionsDataProvider
     *
     * @param mixed $apiVersion
     */
    public function testCorrectApiVersions($apiVersion) {
        $config = new Config('phprest', $apiVersion, true);

        $this->assertEquals($apiVersion, $config->getApiVersion());
    }

    public function correctApiVersionsDataProvider() {
        return [
            [0], [1], [2], [3], [4], [5], [6], [7], [8], [9],
            ['0.0'], ['0.1'], ['0.2'], ['0.8'], ['0.9'],
            ['1.0'], ['1.1'], ['1.2'], ['1.8'], ['1.9'],
            ['2.0'], ['2.1'], ['2.2'], ['2.8'], ['2.9'],
            ['3.0'], ['3.1'], ['3.2'], ['3.8'], ['3.9'],
            ['4.0'], ['4.1'], ['4.2'], ['4.8'], ['4.9'],
            ['5.0'], ['5.1'], ['5.2'], ['5.8'], ['5.9'],
            ['6.0'], ['6.1'], ['6.2'], ['6.8'], ['6.9'],
            ['7.0'], ['7.1'], ['7.2'], ['7.8'], ['7.9'],
            ['8.0'], ['8.1'], ['8.2'], ['8.8'], ['8.9'],
            ['9.0'], ['9.1'], ['9.2'], ['9.8'], ['9.9']
        ];
    }

    /**
     * @dataProvider inCorrectApiVersionsDataProvider
     *
     * @expectedException \InvalidArgumentException
     *
     * @param mixed $apiVersion
     */
    public function testInCorrectApiVersions($apiVersion) {
        $config = new Config('phprest', $apiVersion, true);

        $this->assertEquals($apiVersion, $config->getApiVersion());
    }

    public function inCorrectApiVersionsDataProvider() {
        return [
            [-2], [-1], [10], [11], [12],
            ['a'], [null],
            ['1.0.0'],
            ['1.2.3.4'],
            ['10.1'],
            ['1.10']
        ];
    }

    public function testGetters() {
        $config = new Config('phprest', 1, true);

        $this->assertEquals('phprest', $config->getVendor());
        $this->assertEquals(1, $config->getApiVersion());
        $this->assertEquals(true, $config->isDebug());
        $this->assertInstanceOf('\League\Container\Container', $config->getContainer());
        $this->assertInstanceOf('\League\Route\RouteCollection', $config->getRouter());
        $this->assertInstanceOf('\League\Event\Emitter', $config->getEventEmitter());
        $this->assertInstanceOf('\Phprest\Service\Hateoas\Config', $config->getHateoasConfig());
        $this->assertInstanceOf('\Phprest\Service\Hateoas\Service', $config->getHateoasService());
    }

    public function testLoggerGetterSetter() {
        $config = new Config('phprest', 1, true);

        $config->setLoggerConfig(new LoggerConfig('test'));
        $config->setLoggerService(new LoggerService());

        $this->assertInstanceOf('\Phprest\Service\Logger\Config', $config->getLoggerConfig());
        $this->assertInstanceOf('\Phprest\Service\Logger\Service', $config->getLoggerService());
    }
}
