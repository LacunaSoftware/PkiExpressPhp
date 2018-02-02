<?php

namespace Lacuna\PkiExpress;

/**
 * Class VersionManager
 * @package Lacuna\PkiExpress
 *
 * @property-read $minVersion string
 */
class VersionManager
{
    private $_minVersion = "0.0";

    public function requireVersion($minVersionCandidate)
    {
        if (version_compare($minVersionCandidate, $this->_minVersion) >= 0) {
            $this->_minVersion = $minVersionCandidate;
        }
    }

    public function requireMinVersionFlag()
    {
        return version_compare($this->_minVersion, "1.3") >= 0;
    }

    public function getMinVersion()
    {
        return $this->_minVersion;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "minVersion":
                return $this->getMinVersion();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $prop);
                return null;
        }
    }
}