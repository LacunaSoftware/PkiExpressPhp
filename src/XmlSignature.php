<?php

namespace Lacuna\PkiExpress;

/**
 * Class XmlSignature
 * @package Lacuna\PkiExpress
 *
 * @property-read $signers XmlSignerInfo[]
 */
class XmlSignature
{
    private $_signers = [];

    public function __construct($model)
    {
        foreach ($model->signers as $signer) {
            $this->_signers[] = new XmlSignerInfo($signer);
        }
    }

    /**
     * Gets the array of XML signer info instances.
     *
     * @return XmlSignerInfo[] The array of XML signer info instances.
     */
    public function getSigners()
    {
        return $this->_signers;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "signers":
                return $this->getSigners();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}