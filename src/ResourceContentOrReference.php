<?php

namespace Lacuna\PkiExpress;

/**
 * Class ResourceContentOrReference
 * @package Lacuna\PkiExpress
 *
 * @property $url string
 * @property $mimeType string
 * @property $content binary
 */
class ResourceContentOrReference
{
    public $url;
    public $mimeType;
    public $content;

    public function __construct()
    {
    }
}