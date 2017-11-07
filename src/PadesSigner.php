<?php

namespace Lacuna\PkiExpress;


class PadesSigner extends Signer
{
    private $pdfToSignPath;
    private $vrJsonPath;

    public $overwriteOriginalFile;


    public function __construct($config)
    {
        parent::__construct($config);
        $this->overwriteOriginalFile = false;
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

    public function sign()
    {
        if (empty($this->pdfToSignPath)) {
            throw new \Exception("The PDF to be signed was not set");
        }

        if (empty($this->certThumb)) {
            throw new \Exception("The certificate thumbprint was not set");
        }

        if (!$this->overwriteOriginalFile && empty($this->outputFilePath)) {
            throw new \Exception("The output destination was not set");
        }

        $args = array(
            $this->pdfToSignPath,
            $this->certThumb
        );

        // Logic to overwrite original file or use the output file
        if ($this->overwriteOriginalFile) {
            array_push($args, "-ow");
        } else {
            array_push($args, $this->outputFilePath);
        }

        if (!empty($this->vrJsonPath)) {
            array_push($args, "-vr");
            array_push($args, $this->vrJsonPath);
        }

        $response = parent::invoke(parent::COMMAND_SIGN_PADES, $args);
        if ($response->return != 0) {
            throw new \Exception(implode(PHP_EOL, $response->output));
        }
    }
}