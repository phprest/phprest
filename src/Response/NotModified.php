<?php namespace Phrest\Response;

use Phrest\HttpFoundation\Response;

class NotModified extends Response
{
    /**
     * @param string $contentLocation The value of the Content-Location header
     * @param string $eTag The value of the Etag header
     * @param array $headers An array of response headers
     *
     * @throws \InvalidArgumentException When the HTTP status code is not valid
     *
     * @api
     */
    public function __construct($contentLocation, $eTag, $headers = array())
    {
        parent::__construct('', Response::HTTP_NOT_MODIFIED, $headers);

        $this->headers->set('Content-Location', $contentLocation);
        $this->headers->set('Etag', $eTag);
    }
}
