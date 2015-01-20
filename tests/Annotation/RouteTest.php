<?php namespace Phprest\Annotation;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $method
     *
     * @dataProvider methodProvider
     */
    public function testSuccessValidation($method)
    {
        $route = new Route([
            'method' => $method,
            'path' => '/root',
        ]);

        $this->assertEquals($method, $route->method);
        $this->assertEquals('/root', $route->path);
    }

    public function methodProvider()
    {
        return [
            ['GET'], ['POST'], ['PUT'], ['PATCH'], ['OPTIONS'], ['DELETE'], ['HEAD']
        ];
    }

    public function testSince()
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3'
        ]);

        $this->assertEquals('{version:(?:[2-9]\.[3-9])|(?:[3-9]\.\d)}', $route->version);
    }

    public function testUntil()
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'until' => '3.2'
        ]);

        $this->assertEquals('{version:(?:[0-3]\.[0-2])|(?:[0-2]\.\d)}', $route->version);
    }

    public function testSinceAndUntilWithOneVersionNumDiff()
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3',
            'until' => '3.2'
        ]);

        $this->assertEquals('{version:(?:2\.[3-9])|(?:3\.[0-2])}', $route->version);
    }

    public function testSinceAndUntilWithMoreThanOneVersionNumDiff()
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3',
            'until' => '5.2'
        ]);

        $this->assertEquals('{version:(?:2\.[3-9])|(?:5\.[0-2])|(?:[3-4]\.\d)}', $route->version);
    }

    public function testSinceAndUntilWithEqualFirstNum()
    {
        $route = new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3',
            'until' => '2.7'
        ]);

        $this->assertEquals('{version:(?:2\.[3-7])}', $route->version);
    }

    /**
     * @expectedException \LogicException
     */
    public function testInvalidSinceAndUntil()
    {
        new Route([
            'method' => 'GET',
            'path' => '/root',
            'since' => '2.3',
            'until' => '1.7'
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMethodMissingOnValidation()
    {
        new Route(['path' => '/root']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMethodIsNotCorrectOnValidation()
    {
        new Route(['method' => 'IronMan']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPathMissingOnValidation()
    {
        new Route(['method' => 'POST']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSinceVersionOnValidation()
    {
        new Route(['method' => 'DELETE', 'path' => '/', 'since' => '1.0.1.0']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidUntilVersionOnValidation()
    {
        new Route(['method' => 'DELETE', 'path' => '/', 'until' => '-5']);
    }
}
