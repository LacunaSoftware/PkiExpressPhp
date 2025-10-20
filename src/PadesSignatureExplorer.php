<?php

namespace Lacuna\PkiExpress;

/**
 * Class PadesSignatureExplorer
 * @package Lacuna\PkiExpress
 */
class PadesSignatureExplorer extends SignatureExplorer
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
     * Opens the PAdES signature.
     *
     * @return PadesSignature The content of the signature.
     * @throws \Exception If the signature file is not provided.
     */
    public function open()
    {
        if (empty($this->signatureFilePath)) {
            throw new \Exception("The signature file was not set");
        }

        $args = array(
            $this->signatureFilePath
        );

        if ($this->_validate) {
            array_push($args, "--validate");
        }

        if ($this->validationPolicy) {
            array_push($args, '--policy');
            array_push($args, $this->validationPolicy);

            // This policy can only be used on version greater than 1.20 of the PKI Express.
            $this->versionManager->requireVersion("1.20");
        }

        if ($this->_trustUncertifiedSigningTime) {
            array_push($args, '--trust-uncertified-signing-time');
        }

        // This operation can only be used on versions greater than 1.3 of the PKI Express.
        $this->versionManager->requireVersion("1.3");

        // Invoke command
        $response = parent::invoke(parent::COMMAND_OPEN_PADES, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $signature = new PadesSignature($parsedOutput);

        return $signature;
    }
}