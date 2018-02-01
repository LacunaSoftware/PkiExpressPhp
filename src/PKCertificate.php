<?php

namespace Lacuna\PkiExpress;


class PKCertificate
{
    public $_subjectName;
    public $_emailAddress;
    public $_issuerName;
    public $_serialNumber;
    public $_validityStart;
    public $_validityEnd;
    public $_pkiBrazil;
    public $_pkiItaly;
    public $_issuer;
    public $_binaryThumbprintSHA256;
    public $_thumbprint;


    public function __construct($model)
    {
        $this->_subjectName = $model->subjectName;
        $this->_emailAddress = $model->emailAddress;
        $this->_issuerName = $model->issuerName;
        $this->_serialNumber = $model->serialNumber;
        $this->_validityStart = $model->validityStart;
        $this->_validityEnd = $model->validityEnd;
        if ($model->pkiBrazil) {
            $this->_pkiBrazil = new PkiBrazilCertificateFields($model->pkiBrazil);
        }
        if ($model->pkiItaly) {
            $this->_pkiItaly = new PkiItalyCertificateFields($model->pkiItaly);
        }
        if ($model->issuer) {
            $this->_issuer = new PKCertificate($model->issuer);
        }
        $this->_binaryThumbprintSHA256 = base64_decode($model->binaryThumbprintSHA256);
        $this->_thumbprint = $model->thumbprint;
    }

    public function getSubjectName()
    {
        return $this->_subjectName;
    }

    public function getEmailAddress()
    {
        return $this->_emailAddress;
    }

    public function getIssuerName()
    {
        return $this->_issuerName;
    }

    public function getSerialNumber()
    {
        return $this->_serialNumber;
    }

    public function getValidityStart()
    {
        return $this->_validityStart;
    }

    public function getValidityEnd()
    {
        return $this->_validityEnd;
    }

    public function getPkiBrazil()
    {
        return $this->_pkiBrazil;
    }

    public function getPkiItaly()
    {
        return $this->_pkiItaly;
    }

    public function getIssuer()
    {
        return $this->_issuer;
    }

    public function getBinaryThumbprintSHA256()
    {
        return $this->_binaryThumbprintSHA256;
    }

    public function getThumbprint()
    {
        return $this->_thumbprint;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "subjectName":
                return $this->getSubjectName();
            case "emailAddress":
                return $this->getEmailAddress();
            case "issuerName":
                return $this->getIssuerName();
            case "serialNumber":
                return $this->getSerialNumber();
            case "validityStart":
                return $this->getValidityStart();
            case "validityEnd":
                return $this->getValidityEnd();
            case "pkiBrazil":
                return $this->getPkiBrazil();
            case "pkiItaly":
                return $this->getPkiItaly();
            case "issuer":
                return $this->getIssuer();
            case "binaryThumbprintSHA256":
                return $this->getBinaryThumbprintSHA256();
            case "signatureFieldName":
                return $this->getThumbprint();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}