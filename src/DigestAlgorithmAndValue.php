<?php

namespace Lacuna\PkiExpress;

/**
 * Class DigestAlgorithmAndValue
 * @package Lacuna\PkiExpress
 *
 * @property-read $algorithm DigestAlgorithm
 * @property-read $value binary
 * @property-read $hexValue string
 */
class DigestAlgorithmAndValue
{
    private $_algorithm;
    private $_value;
    private $_hexValue;

    public function __construct($model)
    {
        $this->_algorithm = DigestAlgorithm::getInstanceByCommandAlgorithm($model->algorithm);
        $this->_value = base64_decode($model->value);
        $this->_hexValue = bin2hex($this->_value);
    }

    public function getAlgorithm()
    {
        return $this->_algorithm;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function getHexValue()
    {
        return $this->_hexValue;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "algorithm":
                return $this->getAlgorithm();
            case "value":
                return $this->getValue();
            case 'hexValue':
                return $this->getHexValue();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}