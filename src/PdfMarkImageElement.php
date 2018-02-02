<?php

namespace Lacuna\PkiExpress;

/**
 * Class PdfMarkImageElement
 * @package Lacuna\PkiExpress
 *
 * @property $image
 */
class PdfMarkImageElement extends PdfMarkElement
{
    public $image;


    public function __construct($relativeContainer = null, $image = null)
    {
        parent::__construct(PdfMarkElementType::IMAGE, $relativeContainer);
        $this->image = $image;
    }
}
