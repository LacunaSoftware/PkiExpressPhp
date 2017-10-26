<?php

namespace Lacuna\PkiExpress;


abstract class Signer extends PkiExpressOperator
{
    protected $outputFilePath;
    protected $certThumb;


    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function setOutputFile($path)
    {
        $this->outputFilePath = $path;
    }

    public function setCertificateThumbprint($certThumb)
    {
        $this->certThumb = $certThumb;
    }
}