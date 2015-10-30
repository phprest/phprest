<?php

namespace Phprest\HttpFoundation;

class Response extends \Symfony\Component\HttpFoundation\Response
{
    /**
     * @param mixed $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
