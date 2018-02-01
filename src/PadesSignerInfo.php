<?php

namespace Lacuna\PkiExpress;


class PadesSignerInfo extends CadesSignerInfo
{
    public $_isDocumentTimestamp;
    public $_signatureFieldName;


    public function __construct($model)
    {
        parent::__construct($model);
        $this->_isDocumentTimestamp = $model->isDocumentTimestamp;
        $this->_signatureFieldName = $model->signatureFieldName;
    }

    public function getIsDocumentTimestamp()
    {
        return $this->_isDocumentTimestamp;
    }

    public function getSignatureFieldName()
    {
        return $this->_signatureFieldName;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "messageDigest":
                return $this->getMessageDigest();
            case "signaturePolicy":
                return $this->getSignaturePolicy();
            case "certificate":
                return $this->getCertificate();
            case "signingTime":
                return $this->getSigningTime();
            case "certifiedDateReference":
                return $this->getCertifiedDateReference();
            case "timestamps":
                return $this->getTimestamps();
            case "validationResults":
                return $this->getValidationResults();
            case "isDocumentTimestamp":
                return $this->getIsDocumentTimestamp();
            case "signatureFieldName":
                return $this->getSignatureFieldName();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}