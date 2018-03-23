<?php

namespace Lacuna\PkiExpress;

/**
 * Class PdfMarker
 * @package Lacuna\PkiExpress
 *
 * @property $overwriteOriginalFile bool
 */
class PdfMarker extends PkiExpressOperator
{

    public $measurementUnits;
    public $pageOptimization;
    public $marks;

    private $filePath;
    private $outputFilePath;

    private $_overwriteOriginalFile = false;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
        $this->marks = [];
        $this->measurementUnits = PadesMeasurementUnits::CENTIMETERS;
    }

    //region setFile
    public function setFileFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided file was not found");
        }
        $this->filePath = $path;
    }

    public function setFileFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->filePath = $tempFilePath;
    }

    public function setFileFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided file is not Base64-encoded");
        }

        $this->setFileFromContentRaw($raw);
    }

    public function setFile($path)
    {
        $this->setFileFromPath($path);
    }

    public function setFileContent($contentRaw)
    {
        $this->setFileFromContentRaw($contentRaw);
    }
    //endregion

    public function setOutputFile($path)
    {
        $this->outputFilePath = $path;
    }

    public function apply()
    {
        if (empty($this->filePath)) {
            throw new \Exception("The file to be marked was not set");
        }

        $args = array(
            $this->filePath
        );

        // Generate changes file
        $tempFilePath = parent::createTempFile();
        $request = array(
            'marks' => $this->marks,
            'measurementUnits' => $this->measurementUnits,
            'pageOptimization' => $this->pageOptimization
        );
        file_put_contents($tempFilePath, json_encode($request));
        $args[] = $tempFilePath;

        // Logic to overwrite original file or use the output file
        if ($this->_overwriteOriginalFile) {
            array_push($args, "-ow");
        } else {
            array_push($args, $this->outputFilePath);
        }

        // This operation can only be used on versions greater than 1.3 of the PKI Express.
        $this->versionManager->requireVersion("1.3");

        // Invoke command
        parent::invoke(parent::COMMAND_EDIT_PDF, $args);
    }

    public function getOverwriteOriginalFile()
    {
        return $this->_overwriteOriginalFile;
    }

    public function setOverwriteOriginalFile($value)
    {
        $this->_overwriteOriginalFile = $value;
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