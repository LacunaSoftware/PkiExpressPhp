<?php

namespace Lacuna\PkiExpress;

/**
 * Class CadesSigner
 * @package Lacuna\PkiExpress
 *
 * @property $encapsulateContent bool
 */
class CadesSigner extends Signer
{
    private $fileToSignPath;
    private $dataFilePath;

    private $_encapsulateContent = true;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    public function setFileToSign($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided file to be signed was not found");
        }

        $this->fileToSignPath = $path;
    }

    public function setDataFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided data file was not found");
        }

        $this->dataFilePath = $path;
    }

    public function sign()
    {
        if (empty($this->fileToSignPath)) {
            throw new \Exception("The file to be signed was not set");
        }

        if (empty($this->outputFilePath)) {
            throw new \Exception("The output destination was not set");
        }

        $args = array(
            $this->fileToSignPath,
            $this->outputFilePath
        );

        // Verify and add common options between signers
        parent::verifyAndAddCommonOptions($args);

        if (!empty($this->dataFilePath)) {
            array_push($args, "-df");
            array_push($args, $this->dataFilePath);
        }

        if (!$this->_encapsulateContent) {
            array_push($args, "-det");
        }

        // Invoke command with plain text output (to support PKI Express < 1.3)
        parent::invokePlain(parent::COMMAND_SIGN_CADES, $args);
    }

    public function getEncapsulateContent()
    {
        return $this->_encapsulateContent;
    }

    public function setEncapsulateContent($value)
    {
        $this->_encapsulateContent = $value;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "encapsulateContent":
                return $this->getEncapsulateContent();
            default:
                return parent::__get($prop);
        }
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "encapsulateContent":
                $this->setEncapsulateContent($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }
}