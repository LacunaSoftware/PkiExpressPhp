<?php


namespace Lacuna\PkiExpress;


class CadesSignatureFinisher extends SignatureFinisher2
{
    private $dataHashesPath;
    private $dataFilePath;

    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    public function getDataHashesPath()
    {
        return $this->dataHashesPath;
    }

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
        $models = array();
        foreach($dataHashes as $dh) {
            array_push($models, $dh->toModel());
        }
        if (!($json = json_encode($models))) {
            throw new \Exception("The provided data hashes was not valid");
        }

        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $json);
        $this->dataHashesPath = $tempFilePath;
    }

    public function getDataFilePath()
    {
        return $this->dataFilePath;
    }

    //region setDataFile

    /**
     * Sets the data file's local path.
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
     * Sets the data file's binary content.
     *
     * @param $contentRaw string The content of the data file.
     */
    public function setDataFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->dataFilePath = $tempFilePath;
    }

    /**
     * Sets the data file's Base64-encoded content.
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
     * Sets the data file's local path. This method is only an alias for the setDataFileFromPath() method.
     *
     * @param $path string The path to the data file.
     * @throws \Exception If the provided path is not found.
     */
    public function setDataFile($path)
    {
        $this->setDataFileFromPath($path);
    }

    /**
     * Sets the data file's binary content. This method is only an alias for the setDataFileFromContentRaw() method.
     *
     * @param $contentRaw string The content of the data file.
     */
    public function setDataFileContent($contentRaw)
    {
        $this->setDataFileFromContentRaw($contentRaw);
    }

    //endregion

    /**
     * Completes a signature. This method acts together with start() methods from the SignatureStarter classes.
     *
     * @throws \Exception If the following fields are not provided before this method call:
     *  - The file to be signed;
     *  - The transfer file;
     *  - The signature;
     *  - The output file destination.
     */
    public function complete()
    {
        if (empty($this->fileToSignPath) && empty($this->dataHashesPath)) {
            throw new \Exception("No file or hashes to be signed were set");
        }

        if (empty($this->transferFileId)) {
            throw new \Exception("The transfer file was not set");
        }

        if (empty($this->signature)) {
            throw new \Exception("The signature was not set");
        }

        if (empty($this->outputFilePath)) {
            throw new \Exception("The output destination was not set");
        }

        $args = array(
            $this->config->getTransferDataFolder() . $this->transferFileId,
            $this->signature,
            $this->outputFilePath
        );

        if (!empty($this->fileToSignPath)) {
            array_push($args, "--file");
            array_push($args, $this->fileToSignPath);
        }

        if ($this->dataFilePath) {
            array_push($args, "--data-file");
            array_push($args, $this->dataFilePath);
        }

        if (!empty($this->dataHashesPath)) {
            array_push($args, "--data-hashes");
            array_push($args, $this->dataHashesPath);
        }

        // This operation can only be used on versions greater than 1.18 of the PKI Express.
        $this->versionManager->requireVersion('1.18.0');

        // Invoke command
        $response = parent::invoke(parent::COMMAND_COMPLETE_CADES, $args);

        // Parse output and return model.
        $parsedOutput = $this->parseOutput($response->output[0]);
        return new SignatureResult($parsedOutput);
    }
}