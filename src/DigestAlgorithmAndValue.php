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

    public function __construct($model = null)
    {
        if (isset($model)) {
            $this->_algorithm = DigestAlgorithm::getInstanceByCommandAlgorithm($model->algorithm);
            $this->_value = base64_decode($model->value);
            $this->_hexValue = bin2hex($this->_value);
        }
    }

    /**
     * Gets the digest algorithm.
     *
     * @return DigestAlgorithm The digest algorithm.
     */
    public function getAlgorithm()
    {
        return $this->_algorithm;
    }

    /**
     * Gets the digest algorithm's value.
     *
     * @return binary The digest algorithm's value.
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Gets the digest algorithm's hex value.
     *
     * @return string The digest algorithm's hex value.
     */
    public function getHexValue()
    {
        return $this->_hexValue;
    }

    public function toModel() {
        return [
            "algorithm" => $this->_algorithm->getAlgorithm(),
            "value" => base64_encode($this->_value),
            "hexValue" => $this->_hexValue,
        ];
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