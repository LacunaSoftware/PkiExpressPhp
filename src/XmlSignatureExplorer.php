<?php

namespace Lacuna\PkiExpress;

/**
 * Class XmlSignatureExplorer
 * @package Lacuna\PkiExpress
 */
class XmlSignatureExplorer extends SignatureExplorer
{
    public $validationPolicy;

    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    /**
     * Opens the XML signature.
     *
     * @return XmlSignature The content of the signature.
     * @throws \Exception If the signature file is not provided.
     */
    public function open()
    {
        if (empty($this->signatureFilePath)) {
            throw new \Exception("The signature file was not set");
        }

        $args = [];
        array_push($args, $this->signatureFilePath);

        if ($this->_validate) {
            array_push($args, "--validate");
        }

        if ($this->validationPolicy) {
            array_push($args, '--policy');
            array_push($args, $this->validationPolicy);
        }

        if ($this->_trustUncertifiedSigningTime) {
            array_push($args, '--trust-uncertified-signing-time');
        }

        // This operation can only be used on versions 
        // greater than 1.25.1 of the PKI Express.
        $this->versionManager->requireVersion("1.25.1");

        // Invoke command
        $response = parent::invoke(parent::COMMAND_OPEN_XML, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $signature = new XmlSignature($parsedOutput);

        return $signature;
    }
}