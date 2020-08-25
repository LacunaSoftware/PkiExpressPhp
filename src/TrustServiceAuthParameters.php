<?php

namespace Lacuna\PkiExpress;


/**
 * Class TrustServiceAuthParameters
 * @package Lacuna\PkiExpress
 */
class TrustServiceAuthParameters
{
    private $_authUrl;
    private $_serviceInfo;

    public function __construct($model)
    {
        $this->_authUrl = $model->authUrl;
        if (isset($model->serviceInfo)) {
            $this->_serviceInfo = new TrustServiceInfo($model->serviceInfo);
        }
    }

    /**
     * Gets the authentication url.
     *
     * @return string The authentication url.
     */
    public function getAuthUrl()
    {
        return $this->_authUrl;
    }

    /**
     * Gets the trust service information.
     *
     * @return TrustServiceInfo The trust service information.
     */
    public function getServiceInfo()
    {
        return $this->_serviceInfo;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "serviceInfo":
                return $this->getServiceInfo();
            case "authUrl":
                return $this->getAuthUrl();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}