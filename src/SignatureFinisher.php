<?php

namespace Lacuna\PkiExpress;


class SignatureFinisher extends PkiExpressOperator
{
    private $fileToSignPath;
    private $transferFilePath;
    private $dataFilePath;
    private $outputFilePath;
    private $signature;


    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function setFileToSign($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided file to be signed was not found");
        }

        $this->fileToSignPath = $path;
    }

    public function setTransferFile($path)
    {
        if (!file_exists($this->config->getTempFolder() . $path)) {
            throw new \Exception("The provided transfer file was not found");
        }

        $this->transferFilePath = $path;
    }

    public function setSignature($signature)
    {
        if (!base64_decode($signature)) {
            throw new \Exception("The provided signature was not valid");
        }

        $this->signature = $signature;
    }

    public function setOutputFile($path)
    {
        $this->outputFilePath = $path;
    }

    public function setDataFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided data file was not found");
        }

        $this->dataFilePath = $path;
    }

    public function complete()
    {
        if (empty($this->fileToSignPath)) {
            throw new \Exception("The file to be signed was not set");
        }

        if (empty($this->transferFilePath)) {
            throw new \Exception("The transfer file was not set");
        }

        if (empty($this->signature)) {
            throw new \Exception("The signature was not set");
        }

        if (empty($this->outputFilePath)) {
            throw new \Exception("The output destination was not set");
        }

        $args = array(
            $this->fileToSignPath,
            $this->config->getTempFolder() . $this->transferFilePath,
            $this->signature,
            $this->outputFilePath
        );

        if ($this->dataFilePath) {
            array_push($args, "-df");
            array_push($args, $this->dataFilePath);
        }

        $response = parent::invoke(parent::COMMAND_COMPLETE_SIG, $args);
        if ($response->return != 0) {
            throw new \Exception(implode(PHP_EOL, $response->output));
        }
    }
}