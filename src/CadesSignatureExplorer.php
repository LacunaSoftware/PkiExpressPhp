<?php
/**
 * Created by PhpStorm.
 * User: IsmaelM
 * Date: 05/03/2018
 * Time: 12:24
 */

namespace Lacuna\PkiExpress;


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

    public function setExtractContentPath($path)
    {
        $this->extractContentPath = $path;
    }

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