<?php namespace Phprest\Stub\Controller;

use Phprest\Util\Controller as BaseController;
use Phprest\Response;
use Phprest\Annotation as Phprest;
use Symfony\Component\HttpFoundation\Request;

class Routed extends BaseController
{
    /**
     * @Phprest\Route(method="GET", path="/foos/{id}", since=1.2, until=2.8)
     */
    static public function getFoo(Request $request, $version, $id)
    {
        return new Response\Ok('Hello World!');
    }

    /**
     * @Phprest\Route(method="POST", path="bars", since=0.5, until=0.7)
     */
    static public function postBar(Request $request, $version)
    {
        return new Response\Created('sample location');
    }
}
