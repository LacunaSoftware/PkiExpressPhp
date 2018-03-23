<?php

namespace Lacuna\PkiExpress;

/**
 * Class PadesSignatureStarter
 * @package Lacuna\PkiExpress
 */
class PadesSignatureStarter extends SignatureStarter
{
    private $pdfToSignPath;
    private $vrJsonPath;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    //region setPdfToSign
    public function setPdfToSignFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided PDF to be signed was not found");
        }

        $this->pdfToSignPath = $path;
    }

    public function setPdfToSignFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->pdfToSignPath = $tempFilePath;
    }

    public function setPdfToSignFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided PDF to be signed is not Base64-encoded");
        }

        $this->setPdfToSignFromContentRaw($raw);
    }

    public function setPdfToSign($path)
    {
        $this->setPdfToSignFromPath($path);
    }

    public function setPdfToSignContent($contentRaw)
    {
        $this->setPdfToSignFromContentRaw($contentRaw);
    }
    //endregion

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

        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $json);
        $this->vrJsonPath = $tempFilePath;
    }

    public function start()
    {
        if (empty($this->pdfToSignPath)) {
            throw new \Exception("The PDF to be signed was not set");
        }

        if (empty($this->certificatePath)) {
            throw new \Exception("The certificate was not set");
        }

        // Generate transfer file
        $transferFile = parent::getTransferFileName();

        $args = array(
            $this->pdfToSignPath,
            $this->certificatePath,
            $this->config->getTransferDataFolder() . $transferFile
        );

        if (!empty($this->vrJsonPath)) {
            array_push($args, "--visual-rep");
            array_push($args, $this->vrJsonPath);
        }

        // Invoke command with plain text output (to support PKI Express < 1.3)
        $response = parent::invokePlain(parent::COMMAND_START_PADES, $args);

        // Parse output
        return parent::getResult($response, $transferFile);
    }
}
