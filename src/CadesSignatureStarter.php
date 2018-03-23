<?php

namespace Lacuna\PkiExpress;

/**
 * Class CadesSignatureStarter
 * @package Lacuna\PkiExpress
 *
 * @property $encapsulateContent bool
 */
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

    //region setFileToSign
    public function setFileToSignFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided file to be signed was not found");
        }

        $this->fileToSignPath = $path;
    }

    public function setFileToSignFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->dataFilePath = $tempFilePath;
    }

    public function setFileToSignFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided file to be signed is not Base64-encoded");
        }

        $this->setDataFileFromContentRaw($raw);
    }

    public function setFileToSign($path)
    {
        $this->setFileToSignFromPath($path);
    }

    public function setFileToSignContent($contentRaw)
    {
        $this->setFileToSignFromContentRaw($contentRaw);
    }
    //endregion

    //region setDataFile
    public function setDataFileFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided data file was not found");
        }

        $this->dataFilePath = $path;
    }

    public function setDataFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->dataFilePath = $tempFilePath;
    }

    public function setDataFileFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided data file is not Base64-encoded");
        }

        $this->setDataFileFromContentRaw($raw);
    }

    public function setDataFile($path)
    {
        $this->setDataFileFromPath($path);
    }

    public function setDataFileContent($contentRaw)
    {
        $this->setDataFileFromContentRaw($contentRaw);
    }
    //endregion

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
            array_push($args, "--data-file");
            array_push($args, $this->dataFilePath);
        }

        if (!$this->_encapsulateContent) {
            array_push($args, "--detached");
        }

        // Invoke command with plain text output (to support PKI Express < 1.3)
        $response = parent::invokePlain(parent::COMMAND_START_CADES, $args);

        // Parse output
        return parent::getResult($response, $transferFile);
    }

    public function getEncapsulateContent()
    {
        return $this->_encapsulateContent;
    }

    public function setEncapsulateContent($value)
    {
        $this->_encapsulateContent = $value;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "encapsulateContent":
                return $this->getEncapsulateContent();
            default:
                return parent::__get($prop);
        }
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "encapsulateContent":
                $this->setEncapsulateContent($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }
}