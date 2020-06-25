<?php

namespace Lacuna\PkiExpress;

/**
 * Class PadesSigner
 * @package Lacuna\PkiExpress
 *
 * @property $overwriteOriginalFile bool
 */
class PadesSigner extends Signer
{
    private $pdfToSignPath;
    private $vrJsonPath;

    private $_overwriteOriginalFile = false;
    private $_customSignatureFieldName = null;

    public $suppressDefaultVisualRepresentation = false;
    public $reason;
    public $certificationLevel;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    //region setPdfToSign

    /**
     * Sets PDF to be signed from its path.
     *
     * @param $path string The path to the PDF to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setPdfToSignFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided PDF to be signed was not found");
        }

        $this->pdfToSignPath = $path;
    }

    /**
     * Sets the PDF to be signed from its binary content.
     *
     * @param $contentRaw string The binary content of the PDF to be signed.
     */
    public function setPdfToSignFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->pdfToSignPath = $tempFilePath;
    }

    /**
     * Sets the PDF to be signed from its Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the PDF to be signed.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setPdfToSignFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided PDF to be signed is not Base64-encoded");
        }

        $this->setPdfToSignFromContentRaw($raw);
    }

    /**
     * Sets the PDF to be signed from its path. This method is only an alias for setPdfToSignFromPath() method.
     *
     * @param $path string The path to the PDF to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setPdfToSign($path)
    {
        $this->setPdfToSignFromPath($path);
    }

    /**
     * Sets the PDF to be signed from its binary content. This method is only an alias for setPdfToSignFromContentRaw()
     * method.
     *
     * @param $contentRaw string The binary content of the PDF to be signed.
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
     * Performs the PAdES signature.
     *
     * @throws \Exception If the paths of the file to be signed and the output file are not set.
     */
    public function sign()
    {
        if (empty($this->pdfToSignPath)) {
            throw new \Exception("The PDF to be signed was not set");
        }

        if (!$this->overwriteOriginalFile && empty($this->outputFilePath)) {
            throw new \Exception("The output destination was not set");
        }

        $args = array(
            $this->pdfToSignPath
        );

        // Verify and add common options between signers
        parent::verifyAndAddCommonOptions($args);

        // Logic to overwrite original file or use the output file
        if ($this->_overwriteOriginalFile) {
            array_push($args, "--overwrite");
        } else {
            array_push($args, $this->outputFilePath);
        }

        if (!empty($this->vrJsonPath)) {
            array_push($args, "--visual-rep");
            array_push($args, $this->vrJsonPath);
        }

        if (!empty($this->_customSignatureFieldName)) {
            array_push($args, '--custom-signature-field-name');
            array_push($args, $this->_customSignatureFieldName);

            // This option can only be used on versions greater than 1.15.0 of the PKI Express.
            $this->versionManager->requireVersion('1.15');
        }

        if (!empty($this->certificationLevel)) {
            array_push($args, '--certification-level');
            array_push($args, $this->certificationLevel);

            // This option can only be used on versions greater than 1.16.0 of the PKI Express.
//            $this->versionManager->requireVersion('1.16');
        }

        if (!empty($this->reason)) {
            array_push($args, '--reason');
            array_push($args, $this->reason);

            // This option can only be used on versions greater than 1.13.0 of the PKI Express.
            $this->versionManager->requireVersion('1.13');
        }

        if ($this->suppressDefaultVisualRepresentation) {
            array_push($args, '--suppress-default-visual-rep');

            // This option can only be used on versions greater than 1.13.1 of the PKI Express.
            $this->versionManager->requireVersion('1.13.1');
        }

        // Invoke command with plain text output (to support PKI Express < 1.3)
        parent::invokePlain(parent::COMMAND_SIGN_PADES, $args);
    }

    /**
     * Gets the option to overwrite the original file with the signed file's content.
     *
     * @return bool The option to overwrite the original file.
     */
    public function getOverwriteOriginalFile()
    {
        return $this->_overwriteOriginalFile;
    }

    /**
     * Sets the opiton to overwrite the original file with the signed file's content. The default value for this
     * option is false.
     *
     * @param $value bool The option to overwrite the original file.
     */
    public function setOverwriteOriginalFile($value)
    {
        $this->_overwriteOriginalFile = $value;
    }

    /**
     * Gets the customized signature fieldName.
     *
     * @return string The customized signature fieldName.
     */
    public function getCustomSignatureFieldName()
    {
        return $this->_customSignatureFieldName;
    }

    /**
     * Sets the customized signature fieldName.
     *
     * @param $value string The customized signature fieldName.
     */
    public function setCustomSignatureFieldName($value)
    {
        $this->_customSignatureFieldName = $value;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "overwriteOriginalFile":
                return $this->getOverwriteOriginalFile();
            case "customSignatureFieldName":
                return $this->getCustomSignatureFieldName();
            default:
                return parent::__get($prop);
        }
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "overwriteOriginalFile":
                $this->setOverwriteOriginalFile($value);
                break;
            case "customSignatureFieldName":
                $this->setCustomSignatureFieldName($value);
                break;
            default:
                parent::__set($prop, $value);
        }
    }
}