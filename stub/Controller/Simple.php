<?php

namespace Phprest\Stub\Controller;

use Phprest\HttpFoundation\Response;

class Simple
{
    public static function getTheAnswerOfEverything(): int
    {
        return 42;
    }

    public static function getSampleResponse(): Response
    {
        return new Response('sample');
    }
}
