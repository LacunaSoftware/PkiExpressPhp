<?php

namespace Lacuna\PkiExpress;

/**
 * Class Authentication
 * @package Lacuna\PkiExpress
 *
 * @property-write $useExternalStorage bool
 */
class Authentication extends PkiExpressOperator
{
    private $nonceStorePath;
    private $nonce;
    private $certificatePath;
    private $signature;

    private $_useExternalStorage = true;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    /**
     * Sets the path to be used as PKI Express's local nonce store.
     *
     * @param $path string The path tobe used as PKI Express's local nonce store.
     * @throws \Exception If the provided path was not found or it is not a directory.
     */
    public function setNonceStore($path)
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw new \Exception("The provided nonce store path was not found");
        }
        $this->nonceStorePath = $path;
    }

    /**
     * Sets the Base64-encoded nonce used on complete method.
     *
     * @param $nonce string The Base64-encoded nonce.
     * @throws \Exception If the provided string is not Base64-encoded.
     */
    public function setNonce($nonce)
    {
        if (!base64_decode($nonce)) {
            throw new \Exception("The provided nonce is not valid");
        }

        $this->nonce = $nonce;
    }

    //region setCertificate

    /**
     * Sets the signer certificate's local path.
     *
     * @param $path string The path to the signer certificate.
     * @throws \Exception If the provided path is not found.
     */
    public function setCertificateFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided certificate was not found");
        }
        $this->certificatePath = $path;
    }

    /**
     * Sets the signer certificate's binary content.
     *
     * @param $contentRaw string The content of the signer certificate.
     */
    public function setCertificateFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->certificatePath = $tempFilePath;
    }

    /**
     * Sets the signer certificate's Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the signer certificate.
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
     * Sets the signer certificate's local path. This method is only an alias for the setCertificateFromPath() method.
     *
     * @param $path string The path of the signer certificate.
     * @throws \Exception If the provided path is not found.
     */
    public function setCertificate($path)
    {
        $this->setCertificateFromPath($path);
    }

    /**
     * Sets the signer certificate's binary content. This method is only an alias for the
     * setCertificateFromContentRaw() method.
     *
     * @param $contentRaw string The content of the signer certificate.
     */
    public function setCertificateContent($contentRaw)
    {
        $this->setCertificateFromContentRaw($contentRaw);
    }

    /**
     * Sets the signer certificate's Base64-encoded content. This methos is only an alias for the
     * setCertificateFromContentBase64() method.
     *
     * @param $contentBase64 string The Base64-encoded content of the signer certificate.
     * @throws \Exception If the provided parameter is not Base64 string.
     */
    public function setCertificateBase64($contentBase64)
    {
        $this->setCertificateFromContentBase64($contentBase64);
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
     * Starts an authentication using the PKI Express's "start-auth" command.
     *
     * @return AuthStartResult The result parameters received from the command.
     * @throws \Exception If the option to use external storage is not set and no nonce store path is set.
     */
    public function start()
    {
        $args = [];

        // If chosen to use internal nonce store, pass temp data folder to store the nonces, where PKI Express will
        // verify the nonce against replay-attacks.
        if (!$this->_useExternalStorage) {
            $args[] = "--nonce-store";
            $args[] = $this->config->getTransferDataFolder();
            $this->versionManager->requireVersion("1.4");
        }

        // This operation can only be used on versions greater than 1.4 of the PKI Express.
        $this->versionManager->requireVersion("1.4");

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_START_AUTH, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $result = new AuthStartResult($parsedOutput);

        return $result;
    }

    /**
     * Completes the authentication using the PKI Express's "complete-auth" command.
     *
     * @return AuthCompleteResult The result parameters received from the command.
     * @throws \Exception If the following fields are not provided before this method call:
     *  - The nonce;
     *  - The certificate's path;
     *  - The signature;
     *  - The option to use external storage is not set and no nonce store path is set.
     */
    public function complete()
    {

        if (empty($this->nonce)) {
            throw new \Exception("The nonce value was not set");
        }

        if (empty($this->certificatePath)) {
            throw new \Exception("The certificate file was not set");
        }

        if (empty($this->signature)) {
            throw new \Exception("The signature was not set");
        }

        $args = array(
            $this->nonce,
            $this->certificatePath,
            $this->signature
        );

        // If chosen to use internal nonce store, pass temp data folder to store the nonces, where PKI Express will
        // verify the nonce against replay-attacks.
        if (!$this->_useExternalStorage) {
            $args[] = "--nonce-store";
            $args[] = $this->config->getTransferDataFolder();
            $this->versionManager->requireVersion("1.4");
        }

        // This operation can only be used on versions greater than 1.4 of the PKI Express.
        $this->versionManager->requireVersion("1.4");

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_COMPLETE_AUTH, $args);

        // Parse output.
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response.
        $result = new AuthCompleteResult($parsedOutput);

        return $result;
    }

    /**
     * Sets the option to use external storage.
     *
     * @param $value bool The option to use external storage.
     */
    public function setExternalStorage($value)
    {
        $this->_useExternalStorage = $value;
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "useExternalStorage":
                $this->setExternalStorage($value);
                break;
            default:
                parent::__set($prop, $value);
        }

    }
}