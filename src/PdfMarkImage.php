<?php

namespace Lacuna\PkiExpress;

/**
 * Class PdfMarkImage
 * @package Lacuna\PkiExpress
 *
 * @property $resource ResourceContentOrReference
 * @property $opacity float|null
 */
class PdfMarkImage
{
    public $resource;
    public $opacity;


    public function __construct($imageContent = null, $mimeType = null)
    {
        $this->resource = new ResourceContentOrReference();
        if (!empty($imageContent)) {
            $this->resource->content = base64_encode($imageContent);
        }
        if (!empty($mimeType)) {
            $this->resource->mimeType = $mimeType;
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case "opacity":
                if (isset($this->opacity)) {
                    return $this->opacity;
                } else {
                    return 100;
                }
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name);
                return null;
        }
    }
}
