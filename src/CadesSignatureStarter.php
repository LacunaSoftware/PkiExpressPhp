<?php

namespace Lacuna\PkiExpress;


class CadesSignatureStarter extends SignatureStarter
{
    private $fileToSignPath;
    private $dataFilePath;

    private $_encapsulateContent = true;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
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
            $this->config->getTransferDataFolder() . $transferFile
        );

        if (!empty($this->dataFilePath)) {
            array_push($args, "-df");
            array_push($args, $this->dataFilePath);
        }

        if (!$this->_encapsulateContent) {
            array_push($args, "-det");
        }

        // Invoke command
        $response = parent::invoke(parent::COMMAND_START_CADES, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        return (object)array(
            "toSignHash" => $parsedOutput->toSignHash,
            "digestAlgorithm" => $parsedOutput->digestAlgorithmName,
            "digestAlgorithmOid" => $parsedOutput->digestAlgorithmOid,
            "transferFile" => $transferFile
        );
    }

    public function getEncapsulateContent()
    {
        return $this->_encapsulateContent;
    }

    public function setEncapsulateContent($value)
    {
        $this->_encapsulateContent = $value;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "trustLacunaTestRoot":
                return $this->getTrustLacunaTestRoot();
            case "offline":
                return $this->getOffline();
            case "encapsulateContent":
                return $this->getEncapsulateContent();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }

    public function __set($attr, $value)
    {
        switch ($attr) {
            case "trustLacunaTestRoot":
                $this->setTrustLacunaTestRoot($value);
                break;
            case "offline":
                $this->setOffline($value);
                break;
            case "encapsulateContent":
                $this->setEncapsulateContent($value);
                break;
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}