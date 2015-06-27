<?php namespace Phprest\Entity;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

class DebugError extends Error
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $fileName;

    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    protected $line;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $trace;

    /**
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        parent::__construct($exception);

        $this->fileName = $exception->getFile();
        $this->line     = $exception->getLine();
        $this->trace    = $exception->getTraceAsString();
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return array
     */
    public function getTrace()
    {
        return $this->trace;
    }
}
