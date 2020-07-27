<?php

namespace Lacuna\PkiExpress;

/**
 * Class CadesSignatureStarter
 * @package Lacuna\PkiExpress
 *
 * @property $encapsulateContent bool
 */
class CadesSignatureStarter extends SignatureStarter
{
    private $fileToSignPath;
    private $dataFilePath;
    private $dataHashesPath;

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
     * Sets the local path to the file to be signed.
     *
     * @param $path string The path to the file to be signed.
     * @throws \Exception If the provided file was not found.
     */
    public function setFileToSignFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided file to be signed was not found");
        }

        $this->fileToSignPath = $path;
    }

    /**
     * Sets the content of the file to be signed.
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
     * Sets the Base64-encoded content of the file to be signed.
     *
     * @param $contentBase64 string The Base64-encoded content of the file to be signed.
     * @throws \Exception If the parameter is not Base64-encoded.
     */
    public function setFileToSignFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided file to be signed is not Base64-encoded");
        }

        $this->setFileToSignFromContentRaw($raw);
    }

    /**
     * Sets the local path to the file to be signed. This method is only an alias fo the setFileToSignFromPath() method.
     *
     * @param $path string The path to the file to be signed.
     * @throws \Exception If the provided file was not found.
     */
    public function setFileToSign($path)
    {
        $this->setFileToSignFromPath($path);
    }

    /**
     * Sets the content of the file to be signed.
     *
     * @param $contentRaw string The content of the file to be signed.
     */
    public function setFileToSignContent($contentRaw)
    {
        $this->setFileToSignFromContentRaw($contentRaw);
    }

    //endregion

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
     * Sets the deatched data file's content.
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
     * Sets the detached data file's content Base64-encoded.
     *
     * @param $contentBase64 string The Base64-encoded content.
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
     * Sets the data hashes file's path. This file is a JSON representing a
     * model that has the information to build a list of data hashes for the
     * signature. If preferred, the pure PHP object can be provided using the
     * method setDataHashes().
     *
     * @param $path string The path to the data hashes file's path.
     * @throws \Exception if the provided file is not found.
     */
    public function setDataHashesFromFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided data hashes file was not found");
        }

        $this->dataHashesPath = $path;
    }

    /**
     * Sets the list of data hashes by passing a pure PHP model. If preferred,
     * the JSON file can be provided using the method setDataHashes().
     *
     * @param $dataHashes DigestAlgorithmAndValue[] The list of data hashes.
     * @throws \Exception if the model is invalid, and can't be parsed to a
     * JSON.
     */
    public function setDataHashes($dataHashes)
    {
        if (!($json = json_encode($dataHashes))) {
            throw new \Exception("The provided data hashes was not valid");
        }

        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $json);
        $this->dataHashesPath = $tempFilePath;
    }

    /**
     * @deprecated
     * Starts a CAdES signature.
     *
     * @return mixed The result of the signature init. These values are used by CadesSignatureFinisher.
     * @throws \Exception If the paths to the file to be signed and the certificate are not set.
     */
    public function start()
    {
        if (empty($this->fileToSignPath)) {
            throw new \Exception("The file to be signed was not set");
        }

        if (empty($this->certificatePath)) {
            throw new \Exception("The certificate was not set");
        }

        // Generate transfer file
        $transferFile = parent::getTransferFileName();

        $args = array(
            $this->fileToSignPath,
            $this->certificatePath,
            $this->config->getTransferDataFolder() . $transferFile
        );

        // Verify and add common options between signers
        parent::verifyAndAddCommonOptions($args);

        if (!empty($this->fileToSignPath)) {
            array_push($args, "--file");
            array_push($args, $this->fileToSignPath);
        }

        if (!empty($this->dataFilePath)) {
            array_push($args, "--data-file");
            array_push($args, $this->dataFilePath);
        }

        if (!$this->_encapsulateContent) {
            array_push($args, "--detached");
        }

        // Invoke command with plain text output (to support PKI Express < 1.3)
        $response = parent::invokePlain(parent::COMMAND_START_CADES, $args);

        // Parse output
        return parent::getResult($response, $transferFile);
    }

    /**
     * Starts a CAdES signature. (2nd version)
     *
     * @return mixed The result of the signature init. These values are used by CadesSignatureFinisher.
     * @throws \Exception If the paths to the file to be signed and the certificate are not set.
     */
    public function start2()
    {
        if ($this->_encapsulateContent) {
            if (empty($this->fileToSignPath)) {
                throw new \Exception("The file to be signed was not set");
            }
        } else {
            if (empty($this->fileToSignPath) && empty($this->dataHashesPath)) {
                throw new \Exception("No file or hashes to be signed were set");
            }
        }

        if (empty($this->certificatePath)) {
            throw new \Exception("The certificate was not set");
        }

        // Generate transfer file
        $transferFile = parent::getTransferFileName();

        $args = array(
            $this->certificatePath,
            $this->config->getTransferDataFolder() . $transferFile
        );

        // Verify and add common options between signers
        parent::verifyAndAddCommonOptions($args);

        if (!$this->_encapsulateContent) {
            array_push($args, "--detached");

            if (!empty($this->dataHashesPath)) {
                array_push($args, "--data-hashes");
                array_push($args, $this->dataHashesPath);
            }
        }

        if (!empty($this->fileToSignPath)) {
            array_push($args, "--file");
            array_push($args, $this->fileToSignPath);
        }

        if (!empty($this->dataFilePath)) {
            array_push($args, "--data-file");
            array_push($args, $this->dataFilePath);
        }

        // This operation can only be used on versions greater than 1.17 of the PKI Express.
        $this->versionManager->requireVersion('1.17.0');

        // Invoke command
        $response = parent::invoke(parent::COMMAND_START_CADES2, $args);

        // Parse output and return model.
        $parsedOutput = $this->parseOutput($response->output[0]);
        return new SignatureStartResult($parsedOutput, $transferFile);
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