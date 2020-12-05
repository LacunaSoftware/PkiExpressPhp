<?php


namespace Lacuna\PkiExpress;


class Pkcs12GenerationResult
{
    public $pfx;

    public function __construct($model)
    {
        if (isset($model) && isset($model['pfx'])) {
            $this->pfx = new Pkcs12Certificate($model['pfx']);
        }
    }
}