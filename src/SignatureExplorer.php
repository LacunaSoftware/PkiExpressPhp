<?php

namespace Lacuna\PkiExpress;

/**
 * Class SignatureExplorer
 * @package Lacuna\PkiExpress
 *
 * @property $validate bool
 */
class SignatureExplorer extends PkiExpressOperator
{
    protected $signatureFilePath;

    protected $_validate;

    //region setSignatureFile

    /**
     * Sets the signature file's local path.
     *
     * @param $path string The path to the signature file.
     * @throws \Exception If the provided path is not found.
     */
    public function setSignatureFileFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided signature file was not found");
        }
        $this->signatureFilePath = $path;
    }

    /**
     * Sets the signature file's binary content.
     *
     * @param $contentRaw string The content of the signature file.
     */
    public function setSignatureFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->signatureFilePath = $tempFilePath;
    }

    /**
     * Sets the signature file's Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setSignatureFileFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided signature file is not Base64-encoded");
        }

        $this->setSignatureFileFromContentRaw($raw);
    }

    /**
     * Sets the signature file's local path. This method is only an alias for the setSignatureFileFromPath() method.
     *
     * @param $path string The path to the signature file.
     * @throws \Exception If the provided path is not found.
     */
    public function setSignatureFile($path)
    {
        $this->setSignatureFileFromPath($path);
    }

    /**
     * Sets the signature file's binary content.
     *
     * @param $contentRaw string The content of the signature file.
     */
    public function setSignatureFileContent($contentRaw)
    {
        $this->setSignatureFileFromContentRaw($contentRaw);
    }

    //endregion

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

    public function __get($prop)
    {
        switch ($prop) {
            case "validate":
                return $this->getValidate();
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
            default:
                parent::__set($prop, $value);
        }
    }
}