<?php

namespace Lacuna\PkiExpress;


class PkiExpressConfig
{
    private $pkiExpressHome;
    private $tempFolder;
    private $transferDataFolder;


    public function __construct($pkiExpressHome = null, $tempFolder = null, $transferDataFolder = null)
    {
        if (isset($tempFolder) && file_exists($tempFolder)) {
            $this->tempFolder = $tempFolder . DIRECTORY_SEPARATOR;
        } else {
            $this->tempFolder = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        }

        if (isset($transferDataFolder) && file_exists($transferDataFolder)) {
            $this->transferDataFolder = $transferDataFolder . DIRECTORY_SEPARATOR;
        } else {
            $this->transferDataFolder = $this->tempFolder;
        }

        $this->pkiExpressHome = $pkiExpressHome;
    }

    public function getPkiExpressHome()
    {
        return $this->pkiExpressHome;
    }

    public function getTempFolder()
    {
        return $this->tempFolder;
    }

    public function getTransferDataFolder()
    {
        return $this->transferDataFolder;
    }
}