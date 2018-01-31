<?php

namespace Lacuna\PkiExpress;


class SignaturePolicyIdentifier
{
    public $_digest;
    public $_oid;
    public $_uri;

    public function __construct($model)
    {
        $this->_digest = new DigestAlgorithmAndValue($model);
        $this->_oid = $model->oid;
        $this->_uri = $model->uri;
    }

    public function getDigest()
    {
        return $this->_digest;
    }

    public function getOid()
    {
        return $this->_oid;
    }

    public function getUri()
    {
        return $this->_uri;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "digest":
                return $this->getDigest();
            case "oid":
                return $this->getOid();
            case "uri":
                return $this->getUri();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}