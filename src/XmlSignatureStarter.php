<?php

namespace Lacuna\PkiExpress;


class XmlSignatureStarter extends SignatureStarter
{
    private $xmlToSignPath;
    private $toSignElementId;
    private $signaturePolicy;


    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function setXmlToSign($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided XML to be signed was not found");
        }

        $this->xmlToSignPath = $path;
    }

    public function setToSignElementId($elementId)
    {
        $this->toSignElementId = $elementId;
    }

    public function setSignaturePolicy($policy)
    {
        $this->signaturePolicy = $policy;
    }

    public function start()
    {
        if (empty($this->xmlToSignPath)) {
            throw new \Exception("The XML to be signed was not set");
        }

        if (empty($this->certificatePath)) {
            throw new \Exception("The certificate was not set");
        }

        if ($this->signaturePolicy == XmlSignaturePolicies::NFE && empty($this->toSignElementId)) {
            throw new \Exception("The signature element was not set");
        }

        // Generate transfer file
        $transferFile = parent::getTransferFileName();

        $args = array(
            $this->xmlToSignPath,
            $this->certificatePath,
            $this->config->getTransferDataFolder() . $transferFile
        );

        if (isset($this->signaturePolicy)) {

            array_push($args, "-p");
            array_push($args, $this->signaturePolicy);

            if ($this->signaturePolicy == XmlSignaturePolicies::NFE && isset($this->toSignElementId)) {

                array_push($args, "-eid");
                array_push($args, $this->toSignElementId);
            }
        }

        $response = parent::invoke(parent::COMMAND_START_XML, $args);
        if ($response->return != 0) {
            throw new \Exception(implode(PHP_EOL, $response->output));
        }

        return (object)array(
            "toSignHash" => $response->output[0],
            "digestAlgorithm" => $response->output[1],
            "digestAlgorithmOid" => $response->output[2],
            "transferFile" => $transferFile
        );
    }
}