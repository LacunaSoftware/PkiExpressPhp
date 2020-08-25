<?php

namespace Lacuna\PkiExpress;


/**
 * Class TrustServiceNameModel
 * @package Lacuna\PkiExpress
 */
class TrustServiceNameModel
{
    private $_name;

    public function __construct($model)
    {
        $this->_name = $model->name;
    }

    /**
     * Gets the service's name.
     *
     * @return string The service's name.
     */
    public function getName()
    {
        return $this->_name;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "name":
                return $this->getName();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}