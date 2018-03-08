<?php

namespace Lacuna\PkiExpress;

/**
 * Class CadesSignature
 * @package Lacuna\PkiExpress
 *
 * @property-read $signers CadesSignerInfo[]
 */
class CadesSignature
{
    private $_signers = [];

    public function __construct($model)
    {
        foreach ($model->signers as $signerModel) {
            $this->_signers[] = new CadesSignerInfo($signerModel);
        }
    }

    public function getSigners()
    {
        return $this->_signers;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "signers":
                return $this->getSigners();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $prop);
                return null;
        }
    }
}