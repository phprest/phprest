<?php namespace Phrest\Service;

trait Hateoas
{
    /**
     * @return \Hateoas\Hateoas
     */
    public function serviceHateoas()
    {
        return $this->getContainer()->get('Hateoas');
    }
}
