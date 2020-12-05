<?php


namespace Lacuna\PkiExpress;


class KeyGenerationResult
{
    public $key;
    public $csr;

    public function __construct($model)
    {
        $this->key = $model['key'];
        $this->csr = $model['csr'];
    }
}