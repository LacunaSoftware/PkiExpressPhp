<?php

namespace Lacuna\PkiExpress;

/**
 * Class PkiExpressConfig
 * @package Lacuna\PkiExpress
 *
 * @property-read $pkiExpressHome string
 * @property-read $tempFolder string
 * @property-read $transferDataFolder string
 */
class PkiExpressConfig
{
    private $_pkiExpressHome;
    private $_tempFolder;
    private $_transferDataFolder;


    public function __construct($pkiExpressHome = null, $tempFolder = null, $transferDataFolder = null)
    {
        if (isset($pkiExpressHome) && strpos($pkiExpressHome, '.config') !== false) {
            throw new \Exception('Starting on version 1.1.0 of PKI Express, passing a licensing on the PkiExpressConfig constructor is not longer supported!');
        }

        if (isset($tempFolder) && file_exists($tempFolder)) {
            $this->_tempFolder = $tempFolder . DIRECTORY_SEPARATOR;
        } else {
            $this->_tempFolder = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        }

        if (isset($transferDataFolder) && file_exists($transferDataFolder)) {
            $this->_transferDataFolder = $transferDataFolder . DIRECTORY_SEPARATOR;
        } else {
            $this->_transferDataFolder = $this->_tempFolder;
        }

        $this->_pkiExpressHome = $pkiExpressHome;
    }

    public function getPkiExpressHome()
    {
        return $this->_pkiExpressHome;
    }

    public function getTempFolder()
    {
        return $this->_tempFolder;
    }

    public function getTransferDataFolder()
    {
        return $this->_transferDataFolder;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "pkiExpressHome":
                return $this->getPkiExpressHome();
            case "tempFolder":
                return $this->getTempFolder();
            case "transferDataFolder":
                return $this->getTransferDataFolder();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}