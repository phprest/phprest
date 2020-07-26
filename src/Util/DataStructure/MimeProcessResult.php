<?php

namespace Phprest\Util\DataStructure;

class MimeProcessResult
{
    public string $mime;
    public string $vendor;

    /**
     * @var int|string
     */
    public $apiVersion;

    /**
     * json|xml|null
     */
    public ?string $format;

    /**
     * @param int|string $apiVersion
     */
    public function __construct(string $mime, string $vendor, $apiVersion, ?string $format)
    {
        $this->mime         = $mime;
        $this->vendor       = $vendor;
        $this->apiVersion   = $apiVersion;
        $this->format       = $format;
    }
}
