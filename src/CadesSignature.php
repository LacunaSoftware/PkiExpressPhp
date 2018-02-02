<?php

namespace Lacuna\PkiExpress;

/**
 * Class CadesSignature
 * @package Lacuna\PkiExpress
 *
 * @property-read $encapsulatedContentType mixed
 * @property-read $hasEncapsulatedContent bool
 * @property-read $signers CadesSignerInfo[]
 */
class CadesSignature
{
    private $_encapsulatedContentType;
    private $_hasEncapsulatedContent;
    private $_signers = [];

    public function __construct($encapsulatedContentType, $hasEncapsulatedContent, $signers)
    {
        $this->_encapsulatedContentType = $encapsulatedContentType;
        $this->_hasEncapsulatedContent = $hasEncapsulatedContent;
        foreach ($signers as $signerModel) {
            $this->_signers[] = new CadesSignerInfo($signerModel);
        }
    }

    public function getEncapsulatedContentType()
    {
        return $this->_encapsulatedContentType;
    }

    public function getHasEncapsulatedContent()
    {
        return $this->_hasEncapsulatedContent;
    }

    public function getSigners()
    {
        return $this->_signers;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "encapsulatedContentType":
                return $this->getEncapsulatedContentType();
            case "hasEncapsulatedContent":
                return $this->getHasEncapsulatedContent();
            case "signers":
                return $this->getSigners();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $prop);
                return null;
        }
    }
}