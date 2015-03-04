<?php namespace Phprest\Stub\Controller;

use Phprest\HttpFoundation\Response;

class Simple
{
    /**
     * @return integer
     */
    static public function getTheAnswerOfEverything()
    {
        return 42;
    }

    /**
     * @return Response
     */
    static public function getSampleResponse()
    {
        return new Response('sample');
    }
}
