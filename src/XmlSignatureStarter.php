<?php

namespace Lacuna\PkiExpress;

/**
 * Class XmlSignatureStarter
 * @package Lacuna\PkiExpress
 *
 * @property-write $toSignElementId string
 * @property-write $signaturePolicy string
 */
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

    //region setXmlToSign
    public function setXmlToSignFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided XML to be signed was not found");
        }

        $this->xmlToSignPath = $path;
    }

    public function setXmlToSignFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->xmlToSignPath = $tempFilePath;
    }

    public function setXmlToSignFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided XML to be signed is not Base64-encoded");
        }

        $this->setCertificateFromContentRaw($raw);
    }

    public function setXmlToSign($path)
    {
        $this->setCertificateFromPath($path);
    }

    public function setXmlToSignContent($contentRaw)
    {
        $this->setCertificateFromContentRaw($contentRaw);
    }
    //endregion

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

            array_push($args, "--policy");
            array_push($args, $this->_signaturePolicy);

            if ($this->_signaturePolicy == XmlSignaturePolicies::NFE && isset($this->_toSignElementId)) {

                array_push($args, "--element-id");
                array_push($args, $this->_toSignElementId);
            }
        }

        // Invoke command with plain text output (to support PKI Express < 1.3)
        $response = parent::invokePlain(parent::COMMAND_START_XML, $args);

        // Parse output
        return parent::getResult($response, $transferFile);
    }

    public function setToSignElementId($elementId)
    {
        $this->_toSignElementId = $elementId;
    }

    public function setSignaturePolicy($policy)
    {
        $this->_signaturePolicy = $policy;
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "toSignElementId":
                $this->setToSignElementId($value);
                break;
            case "signaturePolicy":
                $this->setSignaturePolicy($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }
}