<?php

namespace Phprest\HttpFoundation;

use Symfony\Component\HttpFoundation\Request as BaseRequest;

class Request extends BaseRequest
{
    /**
     * @param BaseRequest $request
     */
    public function __construct(BaseRequest $request)
    {
        parent::__construct(
            iterator_to_array($request->query),
            iterator_to_array($request->request),
            iterator_to_array($request->attributes),
            iterator_to_array($request->cookies),
            iterator_to_array($request->files),
            iterator_to_array($request->server),
            $request->getContent()
        );
    }

    /**
     * @param int|string $version
     */
    public function setApiVersion($version)
    {
        $this->pathInfo = '/' . $version . $this->getPathInfo();
    }
}
