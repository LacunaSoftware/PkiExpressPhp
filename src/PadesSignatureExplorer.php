<?php

namespace Lacuna\PkiExpress;

/**
 * Class PadesSignatureExplorer
 * @package Lacuna\PkiExpress
 *
 * @property $validate bool
 */
class PadesSignatureExplorer extends PkiExpressOperator
{
    private $signatureFilePath;

    private $_validate;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    public function setSignatureFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided signature file was not found");
        }
        $this->signatureFilePath = $path;
    }

    public function open()
    {
        if (empty($this->signatureFilePath)) {
            throw new \Exception("The signature file was not set");
        }

        $args = array(
            $this->signatureFilePath
        );

        if ($this->_validate) {
            array_push($args, "--validate");
        }

        // This operation can only be used on versions greater than 1.3 of the PKI Express.
        $this->versionManager->requireVersion("1.3");

        // Invoke command
        $response = parent::invoke(parent::COMMAND_OPEN_PADES, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $signature = new PadesSignature($parsedOutput);

        return $signature;
    }

    public function getValidate()
    {
        return $this->_validate;
    }

    public function setValidate($validate)
    {
        $this->_validate = $validate;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "validate":
                return $this->getValidate();
            default:
                return parent::__get($prop);
        }
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "validate":
                $this->setValidate($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }
}