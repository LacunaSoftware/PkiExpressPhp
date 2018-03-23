<?php

namespace Lacuna\PkiExpress;

/**
 * Class SignatureStarter
 * @package Lacuna\PkiExpress
 */
class SignatureStarter extends PkiExpressOperator
{
    protected $certificatePath;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    //region setCertificate
    public function setCertificateFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided certificate was not found");
        }

        $this->certificatePath = $path;
    }

    public function setCertificateFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->certificatePath = $tempFilePath;
    }

    public function setCertificateFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided certificate is not Base64-encoded");
        }

        $this->setCertificateFromContentRaw($raw);
    }

    public function setCertificate($path)
    {
        $this->setCertificateFromPath($path);
    }

    public function setCertificateContent($contentRaw)
    {
        $this->setCertificateFromContentRaw($contentRaw);
    }

    public function setCertificateBase64($contentBase64)
    {
        $this->setCertificateFromContentBase64($contentBase64);
    }
    //endregion

    protected function getResult($response, $transferFile) {
        return (object)array(
            "toSignHash" => $response[0],
            "digestAlgorithm" => $response[1],
            "digestAlgorithmOid" => $response[2],
            "transferFile" => $transferFile
        );
    }
}
