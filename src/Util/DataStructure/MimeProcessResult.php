<?php namespace Phprest\Util\DataStructure;

class MimeProcessResult
{
    /**
     * @var string
     */
    public $mime;

    /**
     * @var string
     */
    public $vendor;

    /**
     * @var integer|string
     */
    public $apiVersion;

    /**
     * @var string json|xml
     */
    public $format;

    /**
     * @param string $mime
     * @param string $vendor
     * @param integer|string $apiVersion
     * @param string $format
     */
    public function __construct($mime, $vendor, $apiVersion, $format)
    {
        $this->mime = $mime;
        $this->vendor = $vendor;
        $this->apiVersion = $apiVersion;
        $this->format = $format;
    }
}
