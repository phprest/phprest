<?php

namespace Phprest\Test\HttpFoundation;

use Phprest\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

class RequestTest extends TestCase
{
    public function testSetApiVersion(): void
    {
        $request = new Request(new BaseRequest());
        $request->setApiVersion('2.7');

        $this->assertEquals('/2.7/', $request->getPathInfo());
    }
}
