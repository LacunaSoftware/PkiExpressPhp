<?php

namespace Lacuna\PkiExpress;

/**
 * Class CadesSigner
 * @package Lacuna\PkiExpress
 *
 * @property $encapsulateContent bool
 */
class CadesSigner extends Signer
{
    private $fileToSignPath;
    private $dataFilePath;

    private $_encapsulateContent = true;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    //region setFileToSign

    /**
     * Sets the file to be signed from its path.
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
     * Sets the file to be signed from its binary content.
     *
     * @param $contentRaw string The binary content of the file to be signed.
     */
    public function setFileToSignFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->fileToSignPath = $tempFilePath;
    }

    /**
     * Sets the file to be signed from its Base64-encoded content.
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
     * Sets the file to be signed from its path. This method is only an alias for setFileToSignFromPath() method.
     *
     * @param $path string The path to the file to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setFileToSign($path)
    {
        $this->setFileToSignFromPath($path);
    }

    /**
     * Sets the file to be signed from its binary content. This method is only an alias for
     * setFileToSignFromContentRaw() method.
     *
     * @param $contentRaw string The binary content of the file to be signed.
     */
    public function setFileToSignContent($contentRaw)
    {
        $this->setFileToSignFromContentRaw($contentRaw);
    }
    //endregion

    //region setDataFile

    /**
     * Sets the data file from its path.
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
     * Sets the data file from its binary content.
     *
     * @param $contentRaw string The binary content of the data file.
     */
    public function setDataFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->dataFilePath = $tempFilePath;
    }

    /**
     * Sets the data file from its Base64-encoded content.
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
     * Sets the data file form its path. This method is only an alias for the setDataFileFromPath() method.
     *
     * @param $path string The path to the data file.
     * @throws \Exception If the provided path is not found.
     */
    public function setDataFile($path)
    {
        $this->setDataFileFromPath($path);
    }

    /**
     * Sets the data file from its binary content. This method is only an alias for the setDataFileFromContentRaw()
     * method.
     *
     * @param $contentRaw string The binary content of the data file.
     */
    public function setDataFileContent($contentRaw)
    {
        $this->setDataFileFromContentRaw($contentRaw);
    }

    //endregion

    /**
     * Performs the CAdES signature.
     *
     * @throws \Exception If the paths of the fie to be signed and the output file are not set.
     */
    public function sign()
    {
        if (empty($this->fileToSignPath)) {
            throw new \Exception("The file to be signed was not set");
        }

        if (empty($this->outputFilePath)) {
            throw new \Exception("The output destination was not set");
        }

        if (CadesSignaturePolicies::requireTimestamp($this->_signaturePolicy) && empty($this->_timestampAuthority)) {
            throw new \Exception("The provided policy requires a timestamp authority and none was provided.");
        }

        $args = array(
            $this->fileToSignPath,
            $this->outputFilePath
        );

        // Verify and add common options between signers
        parent::verifyAndAddCommonOptions($args);

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
                case TimestampAuthority::OAUTH_TOKEN:
                    $cmdArgs[] = '--tsa-ssl-thumbprint';
                    $cmdArgs[] = $this->_timestampAuthority->certThumb;
                    break;
                case TimestampAuthority::SSL:
                    $cmdArgs[] = '--tsa-token';
                    $cmdArgs[] = $this->_timestampAuthority->token;
                    break;
                default:
                    throw new \Exception('Unknown authentication type of the timestamp authority');

            }

            // This option can only be used on versions greater than 1.5 of the PKI Express.
            $this->versionManager->requireVersion("1.5");
        }

        if (!empty($this->dataFilePath)) {
            array_push($args, "--data-file");
            array_push($args, $this->dataFilePath);
        }

        if (!$this->_encapsulateContent) {
            array_push($args, "--detached");
        }

        // Invoke command with plain text output (to support PKI Express < 1.3)
        parent::invokePlain(parent::COMMAND_SIGN_CADES, $args);
    }

    /**
     * Gets the option to encapsulate the original file's content.
     *
     * @return bool The option to encapsulate the original file's content.
     */
    public function getEncapsulateContent()
    {
        return $this->_encapsulateContent;
    }

    /**
     * Sets the option to encapsulated the original file's content.
     *
     * @param $value bool The option to encapsulate the original file's content.
     */
    public function setEncapsulateContent($value)
    {
        $this->_encapsulateContent = $value;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "encapsulateContent":
                return $this->getEncapsulateContent();
            default:
                return parent::__get($prop);
        }
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "encapsulateContent":
                $this->setEncapsulateContent($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }
}