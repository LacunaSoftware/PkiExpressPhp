<?php

namespace Lacuna\PkiExpress;

/**
 * Class SignatureFinisher
 * @package Lacuna\PkiExpress
 */
class SignatureFinisher2 extends PkiExpressOperator
{
    protected $fileToSignPath;
    protected $outputFilePath;
    protected $signature;
    public $transferFileId;

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
}