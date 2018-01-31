<?php

namespace Lacuna\PkiExpress;


class DigestAlgorithmAndValue
{
    public $_algorithm;
    public $_value;

    public function __construct($model)
    {
        $this->_algorithm = DigestAlgorithm::getInstanceByCommandAlgorithm($model->algorithm);
        $this->_value = base64_decode($model->value);
    }

    public function getAlgorithm() {
        return $this->_algorithm;
    }

    public function getValue() {
        return $this->_value;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "algorithm":
                return $this->getAlgorithm();
            case "value":
                return $this->getValue();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}