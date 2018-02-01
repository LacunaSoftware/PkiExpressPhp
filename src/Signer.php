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

    public function __set($attr, $value)
    {
        switch ($attr) {
            case "certThumb":
                $this->setCertificateThumbprint($value);
                break;
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name);
                return null;
        }
    }
}