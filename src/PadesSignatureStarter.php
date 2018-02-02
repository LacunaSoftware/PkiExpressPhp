<?php

namespace Lacuna\PkiExpress;

/**
 * Class PadesSignatureStarter
 * @package Lacuna\PkiExpress
 */
class PadesSignatureStarter extends SignatureStarter
{
    private $pdfToSignPath;
    private $vrJsonPath;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    public function setPdfToSign($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided PDF to be signed was not found");
        }

        $this->pdfToSignPath = $path;
    }

    public function setVisualRepresentationFromFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided visual representation file was not found");
        }

        $this->vrJsonPath = $path;
    }

    public function setVisualRepresentation($vr)
    {
        if (!($json = json_encode($vr))) {
            throw new \Exception("The provided visual representation was not valid");
        };

        $tempFilePath = $this->createTempFile();
        file_put_contents($tempFilePath, $json);
        $this->vrJsonPath = $tempFilePath;
    }

    public function start()
    {
        if (empty($this->pdfToSignPath)) {
            throw new \Exception("The PDF to be signed was not set");
        }

        if (empty($this->certificatePath)) {
            throw new \Exception("The certificate was not set");
        }

        // Generate transfer file
        $transferFile = parent::getTransferFileName();

        $args = array(
            $this->pdfToSignPath,
            $this->certificatePath,
            $this->config->getTransferDataFolder() . $transferFile
        );

        if (!empty($this->vrJsonPath)) {
            array_push($args, "-vr");
            array_push($args, $this->vrJsonPath);
        }

        // Invoke command
        $response = parent::invoke(parent::COMMAND_START_PADES, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        return (object)array(
            "toSignHash" => $parsedOutput->toSignHash,
            "digestAlgorithm" => $parsedOutput->digestAlgorithmName,
            "digestAlgorithmOid" => $parsedOutput->digestAlgorithmOid,
            "transferFile" => $transferFile
        );
    }
}