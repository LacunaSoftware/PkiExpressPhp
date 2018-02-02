<?php


namespace Lacuna\PkiExpress;

/**
 * Class PkiItalyCertificateFields
 * @package Lacuna\PkiExpress
 *
 * @property-read $certificateType string
 * @property-read $codiceFiscale string
 * @property-read $idCarta string
 */
class PkiItalyCertificateFields
{
    private $_certificateType;
    private $_codiceFiscale;
    private $_idCarta;

    public function __construct($model)
    {
        $this->_certificateType = $model->certificateType;
        $this->_codiceFiscale = $model->codiceFiscale;
        $this->_idCarta = $model->idCarta;
    }

    public function getCertificateType()
    {
        return $this->_certificateType;
    }

    public function getCodiceFiscale()
    {
        return $this->_codiceFiscale;
    }

    public function getIdCarta()
    {
        return $this->_idCarta;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "certificateType":
                return $this->getCertificateType();
            case "codiceFiscale":
                return $this->getCodiceFiscale();
            case "idCarta":
                return $this->getIdCarta();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}