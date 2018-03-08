<?php

namespace Lacuna\PkiExpress;

/**
 * Class CadesTimestamp
 * @package Lacuna\PkiExpress
 *
 * @property-read $genTime date
 * @property-read $serialNumber string
 * @property-read $messageImprint mixed
 */
class CadesTimestamp extends CadesSignature
{
    private $_genTime;
    private $_serialNumber;
    private $_messageImprint;

    public function __construct($model)
    {
        parent::__construct($model);
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

    public function __get($prop)
    {
        switch ($prop) {
            case "genTime":
                return $this->getGenTime();
            case "serialNumber":
                return $this->getSerialNumber();
            case "messageImprint":
                return $this->getMessageImprint();
            default:
                return parent::__get($prop);
        }
    }
}