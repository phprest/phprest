<?php namespace Phrest\Entity;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

class DebugError extends Error
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $fileName;

    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    private $line;

    /**
     * @var array
     * @Serializer\Type("array")
     */
    private $trace;

    /**
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        parent::__construct($exception);

        $this->fileName = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = $exception->getTrace();
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
