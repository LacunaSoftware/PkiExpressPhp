<?php

namespace Lacuna\PkiExpress;

/**
 * Class DiscoverServicesResult
 * @package Lacuna\PkiExpress
 */
class DiscoverServicesResult
{
    private $_services = [];
    private $_authParameters = [];

    public function __construct($model)
    {
        if (isset($model->services)) {
            foreach ($model->services as $serviceModel) {
                $this->_services[] = new TrustServiceInfo($serviceModel);
            }
        }
        if (isset($model->authParameters)) {
            foreach ($model->authParameters as $authParameterModel) {
                $this->_authParameters[] = new TrustServiceAuthParameters($authParameterModel);
            }
        }
    }

    /**
     * Gets array of authentication parameters.
     *
     * @return TrustServiceAuthParameters[] The array of authentication parameters.
     */
    public function getAuthParameters()
    {
        return $this->_authParameters;
    }

    /**
     * Gets array of trusted services.
     *
     * @return TrustServiceInfo[] The array of trusted services.
     */
    public function getServices()
    {
        return $this->_services;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "services":
                return $this->getServices();
            case "authParameters":
                return $this->getAuthParameters();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}