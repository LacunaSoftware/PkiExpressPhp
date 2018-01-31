<?php

namespace Lacuna\PkiExpress;


class PadesSignature
{
    public $_signers = [];

    public function __construct($model)
    {
        foreach ($model->signers as $signer) {
            $this->_signers[] = new PadesSignerInfo($signer);
        }
    }

    public function getSigners()
    {
        return $this->_signers;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "signers":
                return $this->getSigners();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}