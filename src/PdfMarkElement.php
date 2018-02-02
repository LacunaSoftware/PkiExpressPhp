<?php

namespace Lacuna\PkiExpress;

/**
 * Class PdfMarkElement
 * @package Lacuna\PkiExpress
 *
 * @property $elementType string
 * @property $relativeContainer mixed|null
 * @property $rotation int
 * @property $opacity int
 */
class PdfMarkElement
{
    public $elementType;
    public $relativeContainer;
    public $rotation;
    public $opacity;


    public function __construct($elementType, $relativeContainer = null)
    {
        $this->rotation = 0;
        $this->elementType = $elementType;
        $this->relativeContainer = $relativeContainer;
        $this->opacity = 100;
    }
}
