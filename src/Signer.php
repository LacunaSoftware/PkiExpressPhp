<?php

namespace Lacuna\PkiExpress;

/**
 * Class Signer
 * @package Lacuna\PkiExpress
 *
 * @property-write $certThumb string
 */
abstract class Signer extends PkiExpressOperator
{
    protected $outputFilePath;

    protected $_certThumb;
    protected $_pkcs12Path;
    protected $_certPassword;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    public function verifyAndAddCommonOptions(&$args)
    {
        if (empty($this->_certThumb) && empty($this->_pkcs12Path)) {
            throw new \RuntimeException("The certificate's thumbprint and the PKCS #12 were not set");
        }

        if (!empty($this->_certThumb)) {
            array_push($args, "--thumbprint");
            array_push($args, $this->_certThumb);
            $this->versionManager->requireVersion("1.3");
        }

        if (!empty($this->_pkcs12Path)) {
            array_push($args, "--pkcs12");
            array_push($args, $this->_pkcs12Path);
            $this->versionManager->requireVersion("1.3");
        }

        if (!empty($this->_certPassword)) {
            array_push($args, "--password");
            array_push($args, $this->_certPassword);
            $this->versionManager->requireVersion("1.3");
        }
    }

    public function setPkcs12($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided PKCS #12 certificate file was not found");
        }

        $this->_pkcs12Path = $path;
    }

    public function setOutputFile($path)
    {
        $this->outputFilePath = $path;
    }

    public function setCertificateThumbprint($certThumb)
    {
        $this->_certThumb = $certThumb;
    }

    public function setCertPassword($certPassword)
    {
        $this->_certPassword = $certPassword;
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "certThumb":
                $this->setCertificateThumbprint($value);
                break;
            case "certPassword":
                $this->setCertPassword($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }
}