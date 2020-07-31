<?php

namespace Lacuna\PkiExpress;


class PadesTimestamper extends PkiExpressOperator
{
    private $pdfPath;
    private $outputFilePath;
    
    private $_overwriteOriginalFile = false;

    /**
     * @return mixed
     */
    public function getPdfPath()
    {
        return $this->pdfPath;
    }

    //region setPdf

    /**
     * Sets PDF to be signed from its path.
     *
     * @param $path string The path to the PDF to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setPdfFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided PDF to be signed was not found");
        }

        $this->pdfPath = $path;
    }

    /**
     * Sets the PDF to be signed from its binary content.
     *
     * @param $contentRaw string The binary content of the PDF to be signed.
     */
    public function setPdfFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->pdfPath = $tempFilePath;
    }

    /**
     * Sets the PDF to be signed from its Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the PDF to be signed.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setPdfFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided PDF to be signed is not Base64-encoded");
        }

        $this->setPdfFromContentRaw($raw);
    }

    /**
     * Sets the PDF to be signed from its path. This method is only an alias for setPdfFromPath() method.
     *
     * @param $path string The path to the PDF to be signed.
     * @throws \Exception If the provided path is not found.
     */
    public function setPdf($path)
    {
        $this->setPdfFromPath($path);
    }

    /**
     * Sets the PDF to be signed from its binary content. This method is only an alias for setPdfFromContentRaw()
     * method.
     *
     * @param $contentRaw string The binary content of the PDF to be signed.
     */
    public function setPdfContent($contentRaw)
    {
        $this->setPdfFromContentRaw($contentRaw);
    }

    //endregion

    /**
     * @return mixed
     */
    public function getOutputFilePath()
    {
        return $this->outputFilePath;
    }
    
    
    /**
     * Sets the path where this command will store the output file.
     *
     * @param $path string The path where this command will store the output file.
     */
    public function setOutputFile($path)
    {
        $this->outputFilePath = $path;
    }

    /**
     * Gets the option to overwrite the original file with the signed file's
     * content.
     *
     * @return bool The option to overwrite the original file.
     */
    public function getOverwriteOriginalFile()
    {
        return $this->_overwriteOriginalFile;
    }

    /**
     * Sets the opiton to overwrite the original file with the signed file's
     * content. The default value for this option is false.
     *
     * @param $value bool The option to overwrite the original file.
     */
    public function setOverwriteOriginalFile($value)
    {
        $this->_overwriteOriginalFile = $value;
    }

    /**
     * Perform a timestamp on a PDF file.
     *
     * @throws \Exception
     */
    public function stamp()
    {
        if (empty($this->pdfPath)) {
            throw new \Exception('The PDF to be timestamped was not set');
        }
        if (!$this->_overwriteOriginalFile && empty($this->outputFilePath)) {
            throw new \Exception('The output destination as not set');
        }

        $args = array(
            $this->pdfPath
        );

        // Add timestamp authority.
        if (isset($this->_timestampAuthority)) {
            $this->_timestampAuthority->addCmdArguments($args);

            // This option can only be used on versions greater than 1.5 of
            // PKI Express.
            $this->versionManager->requireVersion("1.5");
        }

        // Logic to overwrite original file or use the output file
        if ($this->_overwriteOriginalFile) {
            array_push($args, "--overwrite");
        } else {
            array_push($args, $this->outputFilePath);
        }

        // This option can only be used on versions greater than 1.7.0 of
        // PKI Express.
        $this->versionManager->requireVersion('1.7.0');

        // Invoke command.
        parent::invoke(parent::COMMAND_STAMP_PDF, $args);
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "overwriteOriginalFile":
                return $this->getOverwriteOriginalFile();
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
            default:
                parent::__set($prop, $value);
        }
    }
}