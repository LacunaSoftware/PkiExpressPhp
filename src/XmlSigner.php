<?php

namespace Lacuna\PkiExpress;


class XmlSigner extends Signer
{
    private $xmlToSignPath;
    private $toSignElementId;
    private $signaturePolicy;


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

    public function setToSignElementId($elementId)
    {
        $this->toSignElementId = $elementId;
    }

    public function setSignaturePolicy($policy)
    {
        $this->signaturePolicy = $policy;
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

        if ($this->signaturePolicy == XmlSignaturePolicies::NFE && empty($this->toSignElementId)) {
            throw new \Exception("The signature element id to be signed was not set");
        }

        $args = array(
            $this->xmlToSignPath,
            $this->certThumb,
            $this->outputFilePath
        );
        if (!empty($this->signaturePolicy)) {

            array_push($args, "-p");
            array_push($args, $this->signaturePolicy);

            if ($this->signaturePolicy == XmlSignaturePolicies::NFE && !empty($this->toSignElementId)) {
                array_push($args, "-eid");
                array_push($args, $this->toSignElementId);
            }
        }

        $response = parent::invoke(parent::COMMAND_SIGN_XML, $args);
        if ($response->return != 0) {
            throw new \Exception(implode(PHP_EOL, $response->output));
        }
    }
}