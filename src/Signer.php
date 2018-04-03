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
    protected $pkcs12Path;

    protected $_certThumb;
    protected $_certPassword;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    protected function verifyAndAddCommonOptions(&$args)
    {
        if (empty($this->_certThumb) && empty($this->pkcs12Path)) {
            throw new \RuntimeException("The certificate's thumbprint and the PKCS #12 were not set");
        }

        if (!empty($this->_certThumb)) {
            array_push($args, "--thumbprint");
            array_push($args, $this->_certThumb);
            $this->versionManager->requireVersion("1.3");
        }

        if (!empty($this->pkcs12Path)) {
            array_push($args, "--pkcs12");
            array_push($args, $this->pkcs12Path);
            $this->versionManager->requireVersion("1.3");
        }

        if (!empty($this->_certPassword)) {
            array_push($args, "--password");
            array_push($args, $this->_certPassword);
            $this->versionManager->requireVersion("1.3");
        }
    }

    //region setPkcs12

    /**
     * Sets the PKCS #12 certificate's local path.
     *
     * @param $path string The PKCS #12 certificate's local path.
     * @throws \Exception If the provided path is not found.
     */
    public function setPkcs12FromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided PKCS #12 certificate file was not found");
        }

        $this->pkcs12Path = $path;
    }

    /**
     * Sets the PKCS #12 certificate's binary content.
     *
     * @param $contentRaw string The content of the PKCS #12.
     */
    public function setPkcs12FromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->pkcs12Path = $tempFilePath;
    }

    /**
     * Sets the PKCS #12 certificate's Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the PKCS #12.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setPkcs12FromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided certificate is not Base64-encoded");
        }

        $this->setPkcs12FromContentRaw($raw);
    }

    /**
     * Sets the PKCS #12 certificate's local path. This method is only an alias of the setPkcs12FromPath() method.
     *
     * @param $path string The path to the PKCS #12 certificate.
     * @throws \Exception If the provided path is not found.
     */
    public function setPkcs12($path)
    {
        $this->setPkcs12FromPath($path);
    }

    /**
     * Sets the PKCS #12 certificate's binary content. This method is only an alias of the setPkcs12FromContentRaw()
     * method.
     *
     * @param $contentRaw string The content of the PKCS #12 certificate.
     */
    public function setPkcs12Content($contentRaw)
    {
        $this->setPkcs12FromContentRaw($contentRaw);
    }

    //endregion

    /**
     * Sets the path where this command will store the output file.
     *
     * @param $path string The path where this command will store the output file.
     */
    public function setOutputFile($path)
    {
        $this->outputFilePath = $path;
    }

    /**
     * Sets the certificate's thumbprint.
     *
     * @param $certThumb string The certificate's thumbprint.
     */
    public function setCertificateThumbprint($certThumb)
    {
        $this->_certThumb = $certThumb;
    }

    /**
     * Sets the certificate's PIN.
     *
     * @param $certPassword string The certificate's PIN.
     */
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