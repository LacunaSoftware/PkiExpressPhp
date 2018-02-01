<?php

namespace Lacuna\PkiExpress;


class CadesTimestamp extends CadesSignature
{
    public $_genTime;
    public $_serialNumber;
    public $_messageImprint;

    public function __construct($model)
    {
        parent::__construct($model->encapsulatedContentType, $model->hasEncapsulatedContent, $model->signers);
        $this->_genTime = $model->genTime;
        $this->_serialNumber = $model->serialNumber;
        $this->_messageImprint = $model->messageImprint;
    }

    public function getGenTime()
    {
        return $this->_genTime;
    }

    public function getSerialNumber()
    {
        return $this->_serialNumber;
    }

    public function getMessageImprint()
    {
        return $this->_messageImprint;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "encapsulatedContentType":
                return $this->getEncapsulatedContentType();
            case "hasEncapsulatedContent":
                return $this->getHasEncapsulatedContent();
            case "signers":
                return $this->getSigners();
            case "genTime":
                return $this->getGenTime();
            case "serialNumber":
                return $this->getSerialNumber();
            case "messageImprint":
                return $this->getMessageImprint();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}