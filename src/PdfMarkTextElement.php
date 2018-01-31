<?php

namespace Lacuna\PkiExpress;


class PdfMarkTextElement extends PdfMarkElement
{
    public $textSections;
    public $align;


    public function __construct($relativeContainer = null, $textSections = null)
    {
        parent::__construct(PdfMarkElementType::TEXT, $relativeContainer);
        if (empty($textSections)) {
            $this->textSections = array();
        } else {
            $this->textSections = $textSections;
        }
        $this->align = 'Left';
    }
}
