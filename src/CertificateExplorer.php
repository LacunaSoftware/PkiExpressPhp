<?php

namespace Lacuna\PkiExpress;


/**
 * Class CertificateExplorer
 * @package Lacuna\PkiExpress
 */
class CertificateExplorer extends PkiExpressOperator
{
    private $_certificatePath;
    private $_validate;
    private $_fillContent;
    private $_fillIssuer;

    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }


    //region setCertificate

    /**
     * Sets the certificate's local path.
     *
     * @param $path string The path to the certificate.
     * @throws \Exception If the provided path is not found.
     */
    public function setCertificateFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided certificate was not found");
        }
        $this->_certificatePath = $path;
    }

    /**
     * Sets the certificate's binary content.
     *
     * @param $contentRaw string The content of the certificate.
     */
    public function setCertificateFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->_certificatePath = $tempFilePath;
    }

    /**
     * Sets the certificate's Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the certificate.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setCertificateFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided certificate is not Base64-encoded");
        }
        $this->setCertificateFromContentRaw($raw);
    }

    /**
     * Sets the certificate's local path. This method is only an alias for the setCertificateFromPath() method.
     *
     * @param $path string The path of the certificate.
     * @throws \Exception If the provided path is not found.
     */
    public function setCertificate($path)
    {
        $this->setCertificateFromPath($path);
    }

    /**
     * Sets the certificate's binary content. This method is only an alias for the
     * setCertificateFromContentRaw() method.
     *
     * @param $contentRaw string The content of the certificate.
     */
    public function setCertificateContent($contentRaw)
    {
        $this->setCertificateFromContentRaw($contentRaw);
    }

    /**
     * Sets the certificate's Base64-encoded content. This methos is only an alias for the
     * setCertificateFromContentBase64() method.
     *
     * @param $contentBase64 string The Base64-encoded content of the certificate.
     * @throws \Exception If the provided parameter is not Base64 string.
     */
    public function setCertificateBase64($contentBase64)
    {
        $this->setCertificateFromContentBase64($contentBase64);
    }

    //endregion

    // region validate

    /**
     * Gets the option to validate the signature.
     *
     * @return string The option to validate the signature.
     */
    public function getValidate()
    {
        return $this->_validate;
    }

    /**
     * Sets the option to validate the signature.
     *
     * @param $validate string The option to validate the signature.
     */
    public function setValidate($validate)
    {
        $this->_validate = $validate;
    }

    // endregion

    // region fillIssuer

    /**
     * Gets the option to fillIssuer the signature.
     *
     * @return string The option to fillIssuer the signature.
     */
    public function getFillIssuer()
    {
        return $this->_fillIssuer;
    }

    /**
     * Sets the option to fillIssuer the signature.
     *
     * @param $fillIssuer string The option to fillIssuer the signature.
     */
    public function setFillIssuer($fillIssuer)
    {
        $this->_fillIssuer = $fillIssuer;
    }

    // endregion

    // region fillContent

    /**
     * Gets the option to fillContent the signature.
     *
     * @return string The option to fillContent the signature.
     */
    public function getFillContent()
    {
        return $this->_fillContent;
    }

    /**
     * Sets the option to fillContent the signature.
     *
     * @param $fillContent string The option to fillContent the signature.
     */
    public function setFillContent($fillContent)
    {
        $this->_fillContent = $fillContent;
    }

    // endregion

    public function __get($prop)
    {
        switch ($prop) {
            case "validate":
                return $this->getValidate();
            case "fillContent":
                return $this->getFillContent();
            case "fillIssuer":
                return $this->getFillIssuer();
            default:
                return parent::__get($prop);
        }
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "validate":
                $this->setValidate($value);
                break;
            case "fillContent":
                $this->setFillContent($value);
            case "fillIssuer":
                $this->setFillIssuer($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }

    /**
     * Opens the certificate.
     *
     * @return PKCertificate The certificate information.
     * @throws \Exception If the certificate file is not provided.
     */
    public function open()
    {
        if (empty($this->_certificatePath)) {
            throw new \Exception("The provided certificate file was not found");
        }

        $args = [];
        array_push($args, "--file");
        array_push($args, $this->_certificatePath);

        if ($this->_validate) {
            array_push($args, "--validate");
        }

        if ($this->_fillContent) {
            array_push($args, "--fill-content");
            // This operation can only be used on versions greater than 1.22 of the PKI Express.
            $this->versionManager->requireVersion("1.22");
        }

        if ($this->_fillIssuer) {
            array_push($args, "--fill-issuer");
            // This operation can only be used on versions greater than 1.22 of the PKI Express.
            $this->versionManager->requireVersion("1.22");
        }

        // This operation can only be used on versions greater than 1.20 of the PKI Express.
        $this->versionManager->requireVersion("1.20");

        // Invoke command
        $response = parent::invoke(parent::COMMAND_OPEN_CERT, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $result = new CertificateExplorerResult($parsedOutput);

        return $result;
    }

}