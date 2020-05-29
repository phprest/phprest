<?php

namespace Phprest\Response;

use InvalidArgumentException;
use Phprest\HttpFoundation\Response;

class NoContent extends Response
{
    /**
     * @param array $headers An array of response headers
     *
     * @throws InvalidArgumentException When the HTTP status code is not valid
     *
     * @api
     */
    public function __construct($headers = [])
    {
        parent::__construct('', Response::HTTP_NO_CONTENT, $headers);
    }
}
