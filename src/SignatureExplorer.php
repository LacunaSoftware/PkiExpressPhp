<?php

namespace Lacuna\PkiExpress;

/**
 * Class SignatureExplorer
 * @package Lacuna\PkiExpress
 *
 * @property $validate bool
 */
class SignatureExplorer extends PkiExpressOperator
{
    protected $signatureFilePath;

    protected $_validate;


    public function setSignatureFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided signature file was not found");
        }
        $this->signatureFilePath = $path;
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