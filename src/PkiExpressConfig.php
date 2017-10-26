<?php

namespace Lacuna\PkiExpress;


class PkiExpressConfig
{
    private $pkiExpressHome;
    private $licensePath;
    private $tempFolder;


    public function __construct($licensePath, $pkiExpressHome = null, $tempFolder = null)
    {
        if (!file_exists($licensePath)) {
            throw new \Exception("The provided license was not found");
        }

        if (isset($tempFolder) && file_exists($tempFolder)) {
            $this->tempFolder = $tempFolder;
        } else {
            $this->tempFolder = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
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
}