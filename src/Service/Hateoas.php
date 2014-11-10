<?php namespace Phrest\Service;

trait Hateoas
{
    /**
     * @return \Hateoas\Hateoas
     */
    public function srvHateoas()
    {
        return $this->getContainer()->get('Hateoas');
    }
}
