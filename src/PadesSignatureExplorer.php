<?php

namespace Lacuna\PkiExpress;

/**
 * Class PadesSignatureExplorer
 * @package Lacuna\PkiExpress
 */
class PadesSignatureExplorer extends SignatureExplorer
{

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