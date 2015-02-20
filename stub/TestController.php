<?php namespace Phprest\Stub;

use Phprest\HttpFoundation\Response;

class TestController
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
