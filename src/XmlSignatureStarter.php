<?php

namespace Lacuna\PkiExpress;


class XmlSignatureStarter extends SignatureStarter
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

    public function start()
    {
        if (empty($this->xmlToSignPath)) {
            throw new \Exception("The XML to be signed was not set");
        }

        if (empty($this->certificatePath)) {
            throw new \Exception("The certificate was not set");
        }

        if ($this->_signaturePolicy == XmlSignaturePolicies::NFE && empty($this->_toSignElementId)) {
            throw new \Exception("The signature element was not set");
        }

        // Generate transfer file
        $transferFile = parent::getTransferFileName();

        $args = array(
            $this->xmlToSignPath,
            $this->certificatePath,
            $this->config->getTransferDataFolder() . $transferFile
        );

        if (isset($this->_signaturePolicy)) {

            array_push($args, "-p");
            array_push($args, $this->_signaturePolicy);

            if ($this->_signaturePolicy == XmlSignaturePolicies::NFE && isset($this->_toSignElementId)) {

                array_push($args, "-eid");
                array_push($args, $this->_toSignElementId);
            }
        }

        // Invoke command
        $response = parent::invoke(parent::COMMAND_START_XML, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        return (object)array(
            "toSignHash" => $parsedOutput->toSignHash,
            "digestAlgorithm" => $parsedOutput->digestAlgorithmName,
            "digestAlgorithmOid" => $parsedOutput->digestAlgorithmOid,
            "transferFile" => $transferFile
        );
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