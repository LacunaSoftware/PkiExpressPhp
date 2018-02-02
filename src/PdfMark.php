<?php

namespace Lacuna\PkiExpress;

/**
 * Class PdfMark
 * @package Lacuna\PkiExpress
 *
 * @property $container mixed
 * @property $borderWidth float
 * @property $borderColor Color|null
 * @property $backgroundColor Color|null
 * @property $elements array
 * @property $pageOption string
 * @property $pageOptionNumber int
 */
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
