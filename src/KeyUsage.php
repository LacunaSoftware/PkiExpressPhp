<?php


namespace Lacuna\PkiExpress;


class KeyUsage
{
    const NONE = 0;
    const ENCIPHER_ONLY = 1;
    const CRL_SIGN = 2;
    const KEY_CERT_SIGN = 4;
    const KEY_AGREEMENT = 8;
    const DATA_ENCIPHERMENT = 16;
    const KEY_ENCIPHERMENT = 32;
    const NON_REPUDIATION = 64;
    const DIGITAL_SIGNATURE = 128;
    const DECIPHER_ONLY = 32768;

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function contains($flag)
    {
        return ($this->value & $flag) == $flag;
    }

    public function add($flag)
    {
        $this->value = $this->value | $flag;
    }
}
