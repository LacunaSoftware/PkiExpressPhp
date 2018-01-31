<?php

namespace Lacuna\PkiExpress;


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

    public function getEncapsulatedContentType() {
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

    public function __get($name)
    {
        switch ($name) {
            case "encapsulatedContentType":
                return $this->getEncapsulatedContentType();
            case "hasEncapsulatedContent":
                return $this->getHasEncapsulatedContent();
            case "signers":
                return $this->getSigners();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name);
                return null;
        }
    }
}