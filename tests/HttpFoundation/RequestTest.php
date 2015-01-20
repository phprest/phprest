<?php namespace Phprest\HttpFoundation;

use Symfony\Component\HttpFoundation\Request as BaseRequest;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testSetApiVersion()
    {
        $request = new Request(new BaseRequest());
        $request->setApiVersion('2.7');

        $this->assertEquals('/2.7/', $request->getPathInfo());
    }
}
