<?php

namespace Lacuna\PkiExpress;


/**
 * Class CadesSignatureExplorer
 * @package Lacuna\PkiExpress
 */
class CadesSignatureExplorer extends SignatureExplorer
{
    private $dataFilePath;
    private $extractContentPath;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    //region setDataFile

    /**
     * Sets the detached data file's local path.
     *
     * @param $path string The path to the detached data file.
     * @throws \Exception If the provided data file was not found.
     */
    public function setDataFileFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided data file was not found");
        }

        $this->dataFilePath = $path;
    }

    /**
     * Sets the detached data file's content.
     *
     * @param $contentRaw string The content of the detached data file.
     */
    public function setDataFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->dataFilePath = $tempFilePath;
    }

    /**
     * Sets the detached file's content Base64-encoded.
     *
     * @param $contentBase64 string The Base64-encoded content of the detached data file.
     * @throws \Exception If the parameter is not Base64-encoded.
     */
    public function setDataFileFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided data file is not Base64-encoded");
        }

        $this->setDataFileFromContentRaw($raw);
    }

    /**
     * Sets the detached data file's local path. This method is only an alias for the setDataFileFromPath() method.
     *
     * @param $path string The path to the detached data file.
     * @throws \Exception If the provided data file was not found.
     */
    public function setDataFile($path)
    {
        $this->setDataFileFromPath($path);
    }

    /**
     * Sets the detached data file's content. This method is only an alias for the setDataFileFromContentRaw() method.
     *
     * @param $contentRaw string The content of the detached data file.
     */
    public function setDataFileContent($contentRaw)
    {
        $this->setDataFileFromContentRaw($contentRaw);
    }

    //endregion

    /**
     * Sets the path where to store the content extracted by PKI Express from the signature.
     *
     * @param $path string The path for the extract content.
     */
    public function setExtractContentPath($path)
    {
        $this->extractContentPath = $path;
    }

    /**
     * Opens the CAdES signature.
     *
     * @return CadesSignature The content of the signature.
     * @throws \Exception If the signature file is not provided.
     */
    public function open()
    {
        if (empty($this->signatureFilePath)) {
            throw new \Exception("The provided signature file was not found");
        }

        $args = [];
        array_push($args, $this->signatureFilePath);

        if ($this->validate) {
            array_push($args, "--validate");
        }

        if (!empty($this->dataFilePath)) {
            array_push($args, "--data-file");
            array_push($args, $this->dataFilePath);
        }

        if (!empty($this->extractContentPath)) {
            array_push($args, "--extract-content");
            array_push($args, $this->extractContentPath);
        }

        // This operation can only be used on versions greater than 1.3 of the PKI Express.
        $this->versionManager->requireVersion("1.3");

        // Invoke command
        $response = parent::invoke(parent::COMMAND_OPEN_CADES, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $signature = new CadesSignature($parsedOutput);

        return $signature;
    }
}