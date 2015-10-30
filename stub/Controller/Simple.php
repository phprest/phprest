<?php

namespace Phprest\Stub\Controller;

use Phprest\HttpFoundation\Response;

class Simple
{
    /**
     * @return int
     */
    public static function getTheAnswerOfEverything()
    {
        return 42;
    }

    /**
     * @return Response
     */
    public static function getSampleResponse()
    {
        return new Response('sample');
    }
}
