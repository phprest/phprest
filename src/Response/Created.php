<?php

namespace Phprest\Response;

use Phprest\HttpFoundation\Response;

class Created extends Response
{
    /**
     * @param string $location The value of the Location header
     * @param mixed $content The response content, see setContent()
     * @param array $headers An array of response headers
     *
     * @throws \InvalidArgumentException When the HTTP status code is not valid
     *
     * @api
     */
    public function __construct($location, $content = '', $headers = [])
    {
        parent::__construct($content, Response::HTTP_CREATED, $headers);

        $this->headers->set('Location', $location);
    }
}
