<?php

namespace Phprest\Stub\Controller;

use Phprest\Annotation as Phprest;
use Phprest\Response;
use Phprest\Util\Controller as BaseController;

class Routed extends BaseController
{
    /**
     * @Phprest\Route(method="GET", path="/foos/{id}", since=1.2, until=2.8)
     */
    public static function getFoo()
    {
        return new Response\Ok('Hello World!');
    }

    /**
     * @Phprest\Route(method="POST", path="bars", since=0.5, until=0.7)
     */
    public static function postBar()
    {
        return new Response\Created('sample location');
    }
}
