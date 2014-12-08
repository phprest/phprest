<?php namespace Phrest\Annotation;

/**
 * @Annotation
 *
 * @Target("METHOD")
 */
class Route
{
    public $method;

    public $path;

    /**
     * @param mixed $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($options)
    {
        if ( ! isset($options['method'])) {
            throw new \InvalidArgumentException('method property is missing');
        } elseif ( ! isset($options['path'])) {
            throw new \InvalidArgumentException('path property is missing');
        } elseif ( ! in_array($options['method'], ['GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE', 'HEAD'])) {
            throw new \InvalidArgumentException('method is not valid');
        }

        $this->method = $options['method'];
        $this->path = $options['path'];
    }
}
