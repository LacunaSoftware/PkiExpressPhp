<?php

namespace Lacuna\PkiExpress;


class PkiExpressConfig
{
    private $pkiExpressHome;
    private $licensePath;
    private $tempFolder;
    private $transferDataFolder;


    public function __construct($licensePath, $pkiExpressHome = null, $tempFolder = null, $transferDataFolder = null)
    {
        if (!file_exists($licensePath)) {
            throw new \Exception("The provided license was not found");
        }

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
        $this->licensePath = $licensePath;
    }

    public function getPkiExpressHome()
    {
        return $this->pkiExpressHome;
    }

    public function getLicensePath()
    {
        return $this->licensePath;
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