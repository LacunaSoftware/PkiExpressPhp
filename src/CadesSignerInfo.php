<?php

namespace Lacuna\PkiExpress;

/**
 * Class CadesSignerInfo
 * @package Lacuna\PkiExpress
 *
 * @property-read $messageDigest DigestAlgorithmAndValue
 * @property-read $signaturePolicy SignaturePolicyIdentifier
 * @property-read $certificate PKCertificate
 * @property-read $signingTime date
 * @property-read $certifiedDateReference mixed
 * @property-read $timestamps CadesTimestamp[]
 * @property-read $validationResults ValidationResults
 */
class CadesSignerInfo
{
    private $_messageDigest;
    private $_signaturePolicy;
    private $_certificate;
    private $_signingTime;
    private $_certifiedDateReference;
    private $_timestamps = [];
    private $_validationResults;

    public function __construct($model)
    {
        $this->_messageDigest = new DigestAlgorithmAndValue($model->messageDigest);
        $this->_certificate = new PKCertificate($model->certificate);
        $this->_signingTime = $model->signingTime;
        $this->_certifiedDateReference = $model->certifiedDateReference;
        if (isset($model->signaturePolicy)) {
            $this->_signaturePolicy = new SignaturePolicyIdentifier($model->signaturePolicy);
        }
        if (isset($model->timestamps)) {
            foreach ($model->timestamps as $timestampModel) {
                $this->_timestamps[] = new CadesTimestamp($timestampModel);
            }
        }
        if (isset($model->validationResults)) {
            $this->_validationResults = new ValidationResults($model->validationResults);
        }
    }

    public function getMessageDigest()
    {
        return $this->_messageDigest;
    }

    public function getSignaturePolicy()
    {
        return $this->_signaturePolicy;
    }

    public function getCertificate()
    {
        return $this->_certificate;
    }

    public function getSigningTime()
    {
        return $this->_signingTime;
    }

    public function getCertifiedDateReference()
    {
        return $this->_certifiedDateReference;
    }

    public function getTimestamps()
    {
        return $this->_timestamps;
    }

    public function getValidationResults()
    {
        return $this->_validationResults;
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
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}