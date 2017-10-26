<?php

namespace Lacuna\PkiExpress;


class CadesSigner extends Signer
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

    public function sign()
    {
        if (empty($this->fileToSignPath)) {
            throw new \Exception("The file to be signed was not set");
        }

        if (empty($this->certThumb)) {
            throw new \Exception("The certificate thumbprint was not set");
        }

        if (empty($this->outputFilePath)) {
            throw new \Exception("The output destination was not set");
        }

        $args = array(
            $this->fileToSignPath,
            $this->certThumb,
            $this->outputFilePath
        );

        if (!empty($this->dataFilePath)) {
            array_push($args, "-df");
            array_push($args, $this->dataFilePath);
        }

        if (!$this->encapsulateContent) {
            array_push($args, "-det");
        }

        $response = parent::invoke(parent::COMMAND_SIGN_CADES, $args);
        if ($response->return != 0) {
            throw new \Exception(implode(PHP_EOL, $response->output));
        }
    }
}