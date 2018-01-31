<?php

namespace Lacuna\PkiExpress;


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

        if (!$this->_validate) {
            array_push($args, "-v");
        }

        // Invoke command
        $response = parent::invoke(parent::COMMAND_OPEN_PADES, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        return $parsedOutput;
    }

    public function getValidate()
    {
        return $this->_validate;
    }

    public function setValidate($validate)
    {
        $this->_validate = $validate;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "validate":
                return $this->getValidate();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }

    public function __set($attr, $value)
    {
        switch ($attr) {
            case "encapsulateContent":
                $this->setValidate($value);
                break;
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}