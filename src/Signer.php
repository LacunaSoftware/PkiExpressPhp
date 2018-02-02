<?php

namespace Lacuna\PkiExpress;


abstract class Signer extends PkiExpressOperator
{
    protected $outputFilePath;

    protected $_certThumb;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    public function setOutputFile($path)
    {
        $this->outputFilePath = $path;
    }

    public function setCertificateThumbprint($certThumb)
    {
        $this->_certThumb = $certThumb;
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "certThumb":
                $this->setCertificateThumbprint($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }
}