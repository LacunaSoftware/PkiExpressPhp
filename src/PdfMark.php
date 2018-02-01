<?php

namespace Lacuna\PkiExpress;


class PdfMark
{
    public $container;
    public $borderWidth;
    public $borderColor;
    public $backgroundColor;
    public $elements;
    public $pageOption;
    public $pageOptionNumber;

    public function __construct()
    {
        $this->borderWidth = 0.0;
        $this->borderColor = new Color("#000000"); // Black
        $this->backgroundColor = new Color("#FFFFFF", 0); // Transparent
        $this->elements = [];
        $this->pageOption = PdfMarkPageOptions::ALL_PAGES;
    }
}
