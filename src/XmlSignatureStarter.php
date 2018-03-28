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

    /**
     * Sets the XML to be signed's local path.
     *
     * @param $path string The path to the XML to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setXmlToSignFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided XML to be signed was not found");
        }

        $this->xmlToSignPath = $path;
    }

    /**
     * Sets the XML to be signed's binary content.
     *
     * @param $contentRaw string The content of the XML to be signed.
     */
    public function setXmlToSignFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->xmlToSignPath = $tempFilePath;
    }

    /**
     * Sets the XML to be signed's Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the XML to be signed.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setXmlToSignFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided XML to be signed is not Base64-encoded");
        }

        $this->setCertificateFromContentRaw($raw);
    }

    /**
     * Sets the XML to be signed's local path. This method is only an alias for the setXmlToSignFromPath() method.
     *
     * @param $path string The path to the XML to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setXmlToSign($path)
    {
        $this->setCertificateFromPath($path);
    }

    /**
     * Sets the XML to be signed's binary content. This methos is only an alias for the setXmlToSignFromContentRaw()
     * method.
     *
     * @param $contentRaw string The content of the XML to be signed.
     */
    public function setXmlToSignContent($contentRaw)
    {
        $this->setCertificateFromContentRaw($contentRaw);
    }

    //endregion

    /**
     * Starts a XML signature.
     *
     * @return mixed The result of the signature init. These values are used by SignatureFinisher.
     * @throws \Exception If at least one of the following parameters are not provided:
     *  - The XML to be signed;
     *  - The ceritifcate;
     *  - The element's id to be signed, if the NFe policy was set.
     */
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

    /**
     * Sets the element's id to be signed.
     *
     * @param $elementId string The element's id to be signed.
     */
    public function setToSignElementId($elementId)
    {
        $this->_toSignElementId = $elementId;
    }

    /**
     * Sets the signature policy for the signature.
     *
     * @param $policy string The signature policy fo the signature.
     */
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