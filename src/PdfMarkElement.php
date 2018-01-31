<?php

namespace Lacuna\PkiExpress;


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
