<?php

namespace Lacuna\PkiExpress;

/**
 * Class PadesSigner
 * @package Lacuna\PkiExpress
 *
 * @property $overwriteOriginalFile bool
 */
class PadesSigner extends Signer
{
    private $pdfToSignPath;
    private $vrJsonPath;

    private $_overwriteOriginalFile = false;


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

    public function sign()
    {
        if (empty($this->pdfToSignPath)) {
            throw new \Exception("The PDF to be signed was not set");
        }

        if (empty($this->_certThumb)) {
            throw new \Exception("The certificate thumbprint was not set");
        }

        if (!$this->overwriteOriginalFile && empty($this->outputFilePath)) {
            throw new \Exception("The output destination was not set");
        }

        $args = array(
            $this->pdfToSignPath
        );

        if (!empty($this->_certThumb)) {
            array_push($args, "-t");
            array_push($args, $this->_certThumb);
        }

        // Logic to overwrite original file or use the output file
        if ($this->_overwriteOriginalFile) {
            array_push($args, "-ow");
        } else {
            array_push($args, $this->outputFilePath);
        }

        if (!empty($this->vrJsonPath)) {
            array_push($args, "-vr");
            array_push($args, $this->vrJsonPath);
        }

        // Invoke command
        parent::invoke(parent::COMMAND_SIGN_PADES, $args);
    }

    public function getOverwriteOriginalFile()
    {
        return $this->_overwriteOriginalFile;
    }

    public function setOverwriteOriginalFile($value)
    {
        $this->_overwriteOriginalFile = $value;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "overwriteOriginalFile":
                return $this->getOverwriteOriginalFile();
            default:
                return parent::__get($prop);
        }
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "overwriteOriginalFile":
                $this->setOverwriteOriginalFile($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }
}