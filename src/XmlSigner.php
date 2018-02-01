<?php

namespace Lacuna\PkiExpress;


class XmlSigner extends Signer
{
    private $xmlToSignPath;

    private $_toSignElementId;
    private $_signaturePolicy;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    public function setXmlToSign($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided XML to be signed was not found");
        }

        $this->xmlToSignPath = $path;
    }

    public function sign()
    {
        if (empty($this->xmlToSignPath)) {
            throw new \Exception("The XML to be signed was not set");
        }

        if (empty($this->certThumb)) {
            throw new \Exception("The certificate thumbprint was not set");
        }

        if (empty($this->outputFilePath)) {
            throw new \Exception("The output destination was not set");
        }

        if ($this->_signaturePolicy == XmlSignaturePolicies::NFE && empty($this->_toSignElementId)) {
            throw new \Exception("The signature element id to be signed was not set");
        }

        $args = array(
            $this->xmlToSignPath,
            $this->outputFilePath
        );

        if (!empty($this->certThumb)) {
            array_push($args, "-t");
            array_push($args, $this->certThumb);
        }

        if (!empty($this->_signaturePolicy)) {

            array_push($args, "-p");
            array_push($args, $this->_signaturePolicy);

            if ($this->_signaturePolicy == XmlSignaturePolicies::NFE && !empty($this->_toSignElementId)) {
                array_push($args, "-eid");
                array_push($args, $this->_toSignElementId);
            }
        }

        // Invoke command
        parent::invoke(parent::COMMAND_SIGN_XML, $args);
    }

    public function setToSignElementId($elementId)
    {
        $this->_toSignElementId = $elementId;
    }

    public function setSignaturePolicy($policy)
    {
        $this->_signaturePolicy = $policy;
    }

    public function __set($attr, $value)
    {
        switch ($attr) {
            case "trustLacunaTestRoot":
                $this->setTrustLacunaTestRoot($value);
                break;
            case "offline":
                $this->setOffline($value);
                break;
            case "certThumb":
                $this->setCertificateThumbprint($value);
                break;
            case "toSignElementId":
                $this->setToSignElementId($value);
                break;
            case "signaturePolicy":
                $this->setSignaturePolicy($value);
                break;
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}