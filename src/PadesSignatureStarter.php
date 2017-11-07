<?php

namespace Lacuna\PkiExpress;


class PadesSignatureStarter extends SignatureStarter
{
    private $pdfToSignPath;
    private $vrJsonPath;


    public function __construct($config)
    {
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

        $response = parent::invoke(parent::COMMAND_START_PADES, $args);
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