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

    public function __get($prop)
    {
        switch ($prop) {
            case "isDocumentTimestamp":
                return $this->getIsDocumentTimestamp();
            case "signatureFieldName":
                return $this->getSignatureFieldName();
            default:
                return parent::__get($prop);
        }
    }
}