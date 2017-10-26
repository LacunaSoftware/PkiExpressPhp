<?php

namespace Lacuna\PkiExpress;


class CadesSignatureStarter extends SignatureStarter
{
    private $fileToSignPath;
    private $dataFilePath;

    public $encapsulateContent;


    public function __construct($config)
    {
        parent::__construct($config);
        $this->encapsulateContent = true;
    }

    public function setFileToSign($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided file to be signed was not found");
        }

        $this->fileToSignPath = $path;
    }

    public function setDataFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided data file was not found");
        }

        $this->dataFilePath = $path;
    }

    public function start()
    {
        if (empty($this->fileToSignPath)) {
            throw new \Exception("The file to be signed was not set");
        }

        if (empty($this->certificatePath)) {
            throw new \Exception("The certificate was not set");
        }

        // Generate transfer file
        $transferFile = parent::getTransferFileName();

        $args = array(
            $this->fileToSignPath,
            $this->certificatePath,
            $this->config->getTempFolder() . $transferFile
        );

        if (!empty($this->dataFilePath)) {
            array_push($args, "-df");
            array_push($args, $this->dataFilePath);
        }

        if (!$this->encapsulateContent) {
            array_push($args, "-det");
        }

        $response = parent::invoke(parent::COMMAND_START_CADES, $args);
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