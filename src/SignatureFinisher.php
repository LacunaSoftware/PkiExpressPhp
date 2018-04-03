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

    /**
     * Sets the file to be signed's local path.
     *
     * @param $path string The path to the file to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setFileToSignFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided file to be signed was not found");
        }

        $this->fileToSignPath = $path;
    }

    /**
     * Sets the file to be signed's binary content.
     *
     * @param $contentRaw string The content of the file to be signed.
     */
    public function setFileToSignFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->fileToSignPath = $tempFilePath;
    }

    /**
     * Sets the file to be signed's Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the file to be signed.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setFileToSignFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided file to be signed is not Base64-encoded");
        }

        $this->setFileToSignFromContentRaw($raw);
    }

    /**
     * Sets the file to be signed's local path. This method is only an alias for the setFileToSignFromPath() method.
     *
     * @param $path string The path to the file to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setFileToSign($path)
    {
        $this->setFileToSignFromPath($path);
    }

    /**
     * Sets the file to be signed's binary content. This method is only an alias for the setFileToSignFromContentRaw()
     * method.
     *
     * @param $contentRaw string The content of the file to be signed.
     */
    public function setFileToSignContent($contentRaw)
    {
        $this->setFileToSignFromContentRaw($contentRaw);
    }

    //endregion

    //region setTransferFile

    /**
     * Sets the transfer file's local path.
     *
     * @param $path string The transfer file's local path.
     * @throws \Exception If the provided path is not found.
     */
    public function setTransferFileFromPath($path)
    {
        if (!file_exists($this->config->getTransferDataFolder() . $path)) {
            throw new \Exception("The provided transfer file was not found");
        }

        $this->transferFilePath = $path;
    }

    /**
     * Sets the transfer file's binary content.
     *
     * @param $contentRaw string The content of the transfer file.
     */
    public function setTransferFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->transferFilePath = $tempFilePath;
    }

    /**
     * Sets the transfer file's Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the transfer file.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setTransferFileFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided transfer file is not Base64-encoded");
        }

        $this->setTransferFileFromContentRaw($raw);
    }

    /**
     * Sets the transfer file's local path. This method is only an alias for the setTransferFileFromPath() method.
     *
     * @param $path string The path to the transfer file.
     * @throws \Exception If the provided path is not found.
     */
    public function setTransferFile($path)
    {
        $this->setTransferFileFromPath($path);
    }

    /**
     * Sets the transfer file's binary content. This method is only an alias for the setTransferFileFromContentRaw()
     * method.
     *
     * @param $contentRaw string The content of the transfer file.
     */
    public function setTransferFileContent($contentRaw)
    {
        $this->setTransferFileFromContentRaw($contentRaw);
    }

    //endregion

    /**
     * Sets the computed signature value.
     *
     * @param $signature string The Base64-encoded signature value.
     * @throws \Exception If the provided signature is not Base64-encoded.
     */
    public function setSignature($signature)
    {
        if (!base64_decode($signature)) {
            throw new \Exception("The provided signature was not valid");
        }

        $this->signature = $signature;
    }

    /**
     * Sets the path where this command will store the output file.
     *
     * @param $path string The path where this command will store the output file.
     */
    public function setOutputFile($path)
    {
        $this->outputFilePath = $path;
    }

    //region setDataFile

    /**
     * Sets the data file's local path.
     *
     * @param $path string The path to the data file.
     * @throws \Exception If the provided path is not found.
     */
    public function setDataFileFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided data file was not found");
        }

        $this->dataFilePath = $path;
    }

    /**
     * Sets the data file's binary content.
     *
     * @param $contentRaw string The content of the data file.
     */
    public function setDataFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->dataFilePath = $tempFilePath;
    }

    /**
     * Sets the data file's Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the data file.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setDataFileFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided data file is not Base64-encoded");
        }

        $this->setDataFileFromContentRaw($raw);
    }

    /**
     * Sets the data file's local path. This method is only an alias for the setDataFileFromPath() method.
     *
     * @param $path string The path to the data file.
     * @throws \Exception If the provided path is not found.
     */
    public function setDataFile($path)
    {
        $this->setDataFileFromPath($path);
    }

    /**
     * Sets the data file's binary content. This method is only an alias for the setDataFileFromContentRaw() method.
     *
     * @param $contentRaw string The content of the data file.
     */
    public function setDataFileContent($contentRaw)
    {
        $this->setDataFileFromContentRaw($contentRaw);
    }
    //endregion

    /**
     * Completes a signature. This method acts together with start() methods from the SignatureStarter classes.
     *
     * @throws \Exception If the following fields are not provided before this method call:
     *  - The file to be signed;
     *  - The transfer file;
     *  - The signature;
     *  - The output file destination.
     */
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