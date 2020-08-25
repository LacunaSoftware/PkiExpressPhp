<?php

namespace Lacuna\PkiExpress;

/**
 * Class CheckServiceResult
 * @package Lacuna\PkiExpress
 */
class CheckServiceResult
{
    private $_userHasCertificates;

    public function __construct($model)
    {
        $this->_userHasCertificates = $model->userHasCertificates;
    }

    /**
     * Gets the result of the check on the service.
     *
     * @return bool The result of the check on the service.
     */
    public function getUserHasCertificates()
    {
        return $this->_userHasCertificates;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "userHasCertificates":
                return $this->getUserHasCertificates();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}