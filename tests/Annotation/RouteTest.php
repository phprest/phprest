<?php

namespace Phprest\Test\Annotation;

use InvalidArgumentException;
use LogicException;
use Phprest\Annotation\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    /**
     * @param string $method
     *
     * @dataProvider methodProvider
     */
    public function testSuccessValidation($method): void
    {
        $route = new Route([
            'method' => $method,
            'path' => '/root',
        ]);

        $this->assertEquals($method, $route->method);
        $this->assertEquals('/root', $route->path);
    }

    public function methodProvider(): array
    {
        return [
            ['GET'], ['POST'], ['PUT'], ['PATCH'], ['OPTIONS'], ['DELETE'], ['HEAD']
        ];
    }

    public function testSince(): void
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3'
        ]);

        $this->assertEquals('{version:(?:[2-9]\.[3-9])|(?:[3-9]\.\d)}', $route->version);
    }

    public function testUntil(): void
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'until' => '3.2'
        ]);

        $this->assertEquals('{version:(?:[0-3]\.[0-2])|(?:[0-2]\.\d)}', $route->version);
    }

    public function testSinceAndUntilWithOneVersionNumDiff(): void
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3',
            'until' => '3.2'
        ]);

        $this->assertEquals('{version:(?:2\.[3-9])|(?:3\.[0-2])}', $route->version);
    }

    public function testSinceAndUntilWithMoreThanOneVersionNumDiff(): void
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3',
            'until' => '5.2'
        ]);

        $this->assertEquals('{version:(?:2\.[3-9])|(?:5\.[0-2])|(?:[3-4]\.\d)}', $route->version);
    }

    public function testSinceAndUntilWithEqualFirstNum(): void
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3',
            'until' => '2.7'
        ]);

        $this->assertEquals('{version:(?:2\.[3-7])}', $route->version);
    }

    public function testInvalidSinceAndUntil(): void
    {
        $this->expectException(LogicException::class);

        new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3',
            'until' => '1.7'
        ]);
    }

    public function testMethodMissingOnValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Route(['path' => '/root']);
    }

    public function testMethodIsNotCorrectOnValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Route(['method' => 'IronMan']);
    }

    public function testPathMissingOnValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Route(['method' => 'POST']);
    }

    public function testInvalidSinceVersionOnValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Route(['method' => 'DELETE', 'path' => '/', 'since' => '1.0.1.0']);
    }

    public function testInvalidUntilVersionOnValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Route(['method' => 'DELETE', 'path' => '/', 'until' => '-5']);
    }
}
