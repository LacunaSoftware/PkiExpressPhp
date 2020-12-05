<?php


namespace Lacuna\PkiExpress;


class Pkcs12Generator extends PkiExpressOperator
{
    public $key;
    public $certFilePath;
    public $password;

    public function __construct(
        $key = null,
        $certFilePath = null,
        $password = null,
        $config = null
    ) {
        parent::__construct($config);
        $this->key = $key;
        $this->certFilePath = $certFilePath;
        $this->password = $password;
    }

    //region setCertFile

    /**
     * Sets the certificate file's local path.
     *
     * @param $path string The path to the certificate file.
     * @throws \Exception If the provided file was not found.
     */
    public function setCertFileFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided certificate file was not found");
        }

        $this->certFilePath = $path;
    }

    /**
     * Sets the certificate file's content.
     *
     * @param $contentRaw string The content of the certificate file.
     */
    public function setCertFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->certFilePath = $tempFilePath;
    }

    /**
     * Sets the certificate file's content Base64-encoded.
     *
     * @param $contentBase64 string the Base64-encoded content.
     * @throws \Exception If the parameter is not Base64-encoded.
     */
    public function setCertFileFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided certificate file is not Base64-encoded");
        }

        $this->setCertFileFromContentRaw($raw);
    }

    /**
     * Sets the certificate file's local path. This method is only an alias for the setCertFileFromPath() method.
     *
     * @param $path string The path to the certificate file.
     * @throws \Exception If the provided path is not found.
     */
    public function setCertFile($path)
    {
        $this->setCertFileFromPath($path);
    }

    /**
     * Sets the certificate file's content. This method is only an alias for the setCertFileFromContentRaw() method.
     *
     * @param $contentRaw string The content of the certificate file.
     */
    public function setCertFileContent($contentRaw)
    {
        $this->setCertFileFromContentRaw($contentRaw);
    }

    //endregion

    public function generate($password = null)
    {
        if (!isset($this->key)) {
            throw new \RuntimeException('The generated key was not set');
        }

        if (!isset($this->certFilePath)) {
            throw new \RuntimeException('The certificate file was not set');
        }

        if (!isset($password)) {
            $password = $this->password;
        }

        $args = [$this->key, $this->certFilePath];
        if (isset($password)) {
            array_push($args, '--password');
            array_push($args, $password);
        }

        // This operation can only be used on version greater then 1.11 of the
        // PKI Express.
        $this->versionManager->requireVersion('1.11');

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_CREATE_PFX, $args);

        // Parse output.
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response.
        $result = new Pkcs12GenerationResult($parsedOutput);

        return $result;

    }
}