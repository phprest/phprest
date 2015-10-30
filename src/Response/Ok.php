<?php

namespace Phprest\Response;

use Phprest\HttpFoundation\Response;

class Ok extends Response
{
    /**
     * @param mixed $content The response content, see setContent()
     * @param array $headers An array of response headers
     *
     * @throws \InvalidArgumentException When the HTTP status code is not valid
     *
     * @api
     */
    public function __construct($content = '', $headers = [])
    {
        parent::__construct($content, Response::HTTP_OK, $headers);
    }
}
