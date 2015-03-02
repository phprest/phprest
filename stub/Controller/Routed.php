<?php namespace Phprest\Stub\Controller;

use Phprest\Util\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Phprest\Response;
use Phprest\Annotation as Phprest;

class Routed extends BaseController
{
    /**
     * @Phprest\Route(method="GET", path="/foos/{id}", since=1.2, until=2.8)
     */
    public function getFoo(Request $request, $version, $id)
    {
        return new Response\Ok('Hello World!');
    }

    /**
     * @Phprest\Route(method="POST", path="bars", since=0.5, until=0.7)
     */
    public function postBar(Request $request, $version)
    {
        return new Response\Created('sample location');
    }
}
