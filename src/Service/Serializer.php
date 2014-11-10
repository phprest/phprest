<?php namespace Phrest\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait Serializer
{
    /**
     * @param mixed $value
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function srvSerializer($value, Request $request, Response $response)
    {
        return $this->getContainer()->get('Serializer', [$value, $request, $response]);
    }
}
