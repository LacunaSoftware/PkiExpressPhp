<?php

namespace Lacuna\PkiExpress;

/**
 * Class SignatureFinisher
 * @package Lacuna\PkiExpress
 */
class SignatureFinisher extends PkiExpressOperator
{
    private $fileToSignPath;
    private $transferFilePath;
    private $dataFilePath;
    private $outputFilePath;
    private $signature;


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
        $this->fileToSignPath = $tempFilePath;
    }

    public function setFileToSignFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided file to be signed is not Base64-encoded");
        }

        $this->setFileToSignFromContentRaw($raw);
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

    //region setTransferFile
    public function setTransferFileFromPath($path)
    {
        if (!file_exists($this->config->getTransferDataFolder() . $path)) {
            throw new \Exception("The provided transfer file was not found");
        }

        $this->transferFilePath = $path;
    }

    public function setTransferFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->transferFilePath = $tempFilePath;
    }

    public function setTransferFileFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided transfer file is not Base64-encoded");
        }

        $this->setDataFileFromContentRaw($raw);
    }

    public function setTransferFile($path)
    {
        $this->setFileToSignFromPath($path);
    }

    public function setTransferFileContent($contentRaw)
    {
        $this->setFileToSignFromContentRaw($contentRaw);
    }
    //endregion

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

    public function setDataFileContent($contentPath)
    {
        $this->setFileToSignFromContentRaw($contentPath);
    }
    //endregion

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
            $this->config->getTransferDataFolder() . $this->transferFilePath,
            $this->signature,
            $this->outputFilePath
        );

        if ($this->dataFilePath) {
            array_push($args, "--data-file");
            array_push($args, $this->dataFilePath);
        }

        // Invoke command with plain text output (to support PKI Express < 1.3)
        parent::invokePlain(parent::COMMAND_COMPLETE_SIG, $args);
    }
}