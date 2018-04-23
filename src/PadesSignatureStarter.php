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

    /**
     * Sets the PDF to be signed's local path.
     *
     * @param $path string The path to the PDF to be signed.
     * @throws \Exception If the provided file was not found.
     */
    public function setPdfToSignFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided PDF to be signed was not found");
        }

        $this->pdfToSignPath = $path;
    }

    /**
     * Sets the PDF to be signed's content.
     *
     * @param $contentRaw string The content of the PDF to be signed.
     */
    public function setPdfToSignFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->pdfToSignPath = $tempFilePath;
    }

    /**
     * Sets the PDF to be signed's content Base64-encoded.
     *
     * @param $contentBase64 string the Base64-encoded content.
     * @throws \Exception If the parameter is not Base64-encoded.
     */
    public function setPdfToSignFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided PDF to be signed is not Base64-encoded");
        }

        $this->setPdfToSignFromContentRaw($raw);
    }

    /**
     * Sets the PDF to be signed's local path. This method is only an alias for the setPdfToSignFromPath() method.
     *
     * @param $path string The path to the PDF to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setPdfToSign($path)
    {
        $this->setPdfToSignFromPath($path);
    }

    /**
     * Sets the PDF to be signed's content. This method is only an alias for the setPdfToSignFromContentRaw() method.
     *
     * @param $contentRaw string The content of the PDF to be signed.
     */
    public function setPdfToSignContent($contentRaw)
    {
        $this->setPdfToSignFromContentRaw($contentRaw);
    }

    //endregion

    /**
     * Sets the visual representation file's path. This file is a JSON representing a model that has the information
     * to build a visual representation for the signature. If preferred, the pure PHP object can be provided using
     * the method setVisualRepresentation().
     *
     * @param $path string The path to the visual representation file's path.
     * @throws \Exception If the provided file is not found.
     */
    public function setVisualRepresentationFromFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided visual representation file was not found");
        }

        $this->vrJsonPath = $path;
    }

    /**
     * Sets the visual representation by passing a pure PHP model. If preferred, the JSON file can be provided using
     * the method setVisualRepresentationFromFile().
     *
     * @param $vr mixed The visual representation's model.
     * @throws \Exception If the model is invalid, and can't be parsed to a JSON.
     */
    public function setVisualRepresentation($vr)
    {
        if (!($json = json_encode($vr))) {
            throw new \Exception("The provided visual representation was not valid");
        };

        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $json);
        $this->vrJsonPath = $tempFilePath;
    }

    /**
     * Starts a PAdES signature.
     *
     * @return mixed The result of the signature init. These values are used by SignatureFinisher.
     * @throws \Exception If the paths to the file to be signed and the certificate are not set.
     */
    public function start()
    {
        if (empty($this->pdfToSignPath)) {
            throw new \Exception("The PDF to be signed was not set");
        }

        if (empty($this->certificatePath)) {
            throw new \Exception("The certificate was not set");
        }

        if (PadesSignaturePolicies::requireTimestamp($this->_signaturePolicy) && empty($this->_timestampAuthority)) {
            throw new \Exception("The provided policy requires a timestamp authority and none was provided.");
        }

        // Generate transfer file
        $transferFile = parent::getTransferFileName();

        $args = array(
            $this->pdfToSignPath,
            $this->certificatePath,
            $this->config->getTransferDataFolder() . $transferFile
        );

        // Set signature policy.
        if (isset($this->_signaturePolicy)) {
            $cmdArgs[] = '--policy';
            $cmdArgs[] = $this->_signaturePolicy;
        }

        // Add timestamp authority.
        if (isset($this->_timestampAuthority)) {
            $cmdArgs[] = '--tsa-url';
            $cmdArgs[] = $this->_timestampAuthority->url;

            // User choose SSL authentication.
            switch ($this->_timestampAuthority->type) {
                case TimestampAuthority::BASIC_AUTH:
                    $cmdArgs[] = '--tsa-basic-auth';
                    $cmdArgs[] = $this->_timestampAuthority->basicAuth;
                    break;
                case TimestampAuthority::SSL:
                    $cmdArgs[] = '--tsa-ssl-thumbprint';
                    $cmdArgs[] = $this->_timestampAuthority->sslThumbprint;
                    break;
                case TimestampAuthority::OAUTH_TOKEN:
                    $cmdArgs[] = '--tsa-token';
                    $cmdArgs[] = $this->_timestampAuthority->token;
                    break;
                default:
                    throw new \Exception('Unknown authentication type of the timestamp authority');

            }

            // This option can only be used on versions greater than 1.5 of the PKI Express.
            $this->versionManager->requireVersion("1.5");
        }

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
