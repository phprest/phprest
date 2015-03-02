<?php namespace Phprest\Stub\Controller;

use Phprest\HttpFoundation\Response;

class Simple
{
    /**
     * @return integer
     */
    public function getTheAnswerOfEverything()
    {
        return 42;
    }

    /**
     * @return Response
     */
    public function getSampleResponse()
    {
        return new Response('sample');
    }
}
