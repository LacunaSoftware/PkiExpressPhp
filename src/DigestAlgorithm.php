<?php

namespace Lacuna\PkiExpress;

/**
 * Class DigestAlgorithm
 * @package Lacuna\PkiExpress
 *
 * @property $id string
 * @property $name string
 * @property $phpId string
 */
class DigestAlgorithm
{
    const MD5 = 'MD5';
    const SHA1 = 'SHA1';
    const SHA256 = 'SHA256';
    const SHA384 = 'SHA384';
    const SHA512 = 'SHA512';

    private $id;

    private function __construct($id)
    {
        switch ($id) {
            case self::MD5:
            case self::SHA1:
            case self::SHA256:
            case self::SHA384:
            case self::SHA512:
                $this->id = $id;
                break;
            default:
                throw new \RuntimeException("Unsupported digest algorithm: " . $id);
        }
    }

    public static function getMD5()
    {
        return new DigestAlgorithm(self::MD5);
    }

    public static function getSHA1()
    {
        return new DigestAlgorithm(self::SHA1);
    }

    public static function getSHA256()
    {
        return new DigestAlgorithm(self::SHA256);
    }

    public static function getSHA384()
    {
        return new DigestAlgorithm(self::SHA384);
    }

    public static function getSHA512()
    {
        return new DigestAlgorithm(self::SHA512);
    }

    public static function getInstanceByCommandAlgorithm($apiDigestAlg)
    {
        return new DigestAlgorithm($apiDigestAlg);
    }

    public function getAlgorithm()
    {
        return $this->id;
    }

    public function getName()
    {
        switch ($this->id) {
            case self::MD5:
                return 'MD5';
            case self::SHA1:
                return 'SHA-1';
            case self::SHA256:
                return 'SHA-256';
            case self::SHA384:
                return 'SHA-384';
            case self::SHA512:
                return 'SHA-512';
            default:
                throw new \RuntimeException(); // should not happen
        }
    }

    public function getPhpId()
    {
        switch ($this->id) {
            case self::MD5:
                return 'md5';
            case self::SHA1:
                return 'sha1';
            case self::SHA256:
                return 'sha256';
            case self::SHA384:
                return 'sha384';
            case self::SHA512:
                return 'sha512';
            default:
                throw new \RuntimeException(); // should not happen
        }
    }

    public function getHashId()
    {
        switch ($this->id) {
            case self::MD5:
                return MHASH_MD5;
            case self::SHA1:
                return MHASH_SHA1;
            case self::SHA256:
                return MHASH_SHA256;
            case self::SHA384:
                return MHASH_SHA384;
            case self::SHA512:
                return MHASH_SHA512;
            default:
                throw new \RuntimeException(); // should not happen
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case "id":
                return $this->getAlgorithm();
            case "phpId":
                return $this->getPhpId();
            case "name":
                return $this->getName();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name);
                return null;
        }
    }

    public function __toString()
    {
        return $this->getName();
    }
}
