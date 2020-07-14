<?php

namespace Phprest\Test;

use InvalidArgumentException;
use League\BooBoo\BooBoo;
use Phprest\Config;
use Phprest\Service\Logger\Config as LoggerConfig;
use Phprest\Service\Logger\Service as LoggerService;
use Phprest\Service\Hateoas;
use League\Container\Container;
use League\Route\RouteCollection;
use League\Event\Emitter;
use Phprest\ErrorHandler\Handler\Log;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    /**
     * @dataProvider correctApiVersionsDataProvider
     *
     * @param mixed $apiVersion
     */
    public function testCorrectApiVersions($apiVersion): void
    {
        $config = new Config('phprest', $apiVersion, true);

        $this->assertEquals($apiVersion, $config->getApiVersion());
    }

    public function correctApiVersionsDataProvider(): array
    {
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
     * @param mixed $apiVersion
     */
    public function testInCorrectApiVersions($apiVersion): void
    {
        $this->expectException(InvalidArgumentException::class);

        $config = new Config('phprest', $apiVersion, true);

        $this->assertEquals($apiVersion, $config->getApiVersion());
    }

    public function inCorrectApiVersionsDataProvider(): array
    {
        return [
            [-2], [-1], [10], [11], [12],
            ['a'], ['b'],
            ['1.0.0'],
            ['1.2.3.4'],
            ['10.1'],
            ['1.10']
        ];
    }

    public function testGetters(): void
    {
        $config = new Config('phprest', 1, true);

        $this->assertEquals('phprest', $config->getVendor());
        $this->assertEquals(1, $config->getApiVersion());
        $this->assertEquals(true, $config->isDebug());
        $this->assertInstanceOf(Container::class, $config->getContainer());
        $this->assertInstanceOf(RouteCollection::class, $config->getRouter());
        $this->assertInstanceOf(Emitter::class, $config->getEventEmitter());
        $this->assertInstanceOf(Hateoas\Config::class, $config->getHateoasConfig());
        $this->assertInstanceOf(Hateoas\Service::class, $config->getHateoasService());
        $this->assertInstanceOf(BooBoo::class, $config->getErrorHandler());
        $this->assertInstanceOf(Log::class, $config->getLogHandler());
    }

    public function testLoggerGetterSetter(): void
    {
        $config = new Config('phprest', 1, true);

        $loggerConfig = new LoggerConfig('test');
        $loggerService = new LoggerService();

        $config->setLoggerConfig($loggerConfig);
        $config->setLoggerService($loggerService);

        $this->assertSame($loggerConfig, $config->getLoggerConfig());
        $this->assertSame($loggerService, $config->getLoggerService());
    }
}
