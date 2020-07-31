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
     * Sets the digest algorithm.
     *
     * @param $value DigestAlgorithm The digest algorithm.
     */
    public function setAlgorithm($value)
    {
        $this->_algorithm = $value;
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
     * Sets the digest algorithm's value.
     *
     * @param $value binary The digest algorithm's value.
     */
    public function setValue($value)
    {
        $this->_value = $value;
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

    /**
     * Sets the digest algorithm's hex value.
     *
     * @param $value string The digest algorithm's hex value.
     */
    public function setHexValue($value)
    {
        $this->_hexValue = $value;
    }

    public function toModel()
    {
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

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "algorithm":
                $this->setAlgorithm($value);
                break;
            case "value":
                $this->setValue($value);
                break;
            case "hexValue":
                $this->setHexValue($value);
                break;
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}