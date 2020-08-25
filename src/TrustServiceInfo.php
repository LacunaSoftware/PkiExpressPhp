<?php

namespace Lacuna\PkiExpress;


/**
 * Class TrustServiceInfo
 * @package Lacuna\PkiExpress
 */
class TrustServiceInfo
{
    private $_provider;
    private $_badgeUrl;
    private $_service;

    public function __construct($model)
    {
        $this->_provider = $model->provider;
        $this->_badgeUrl = $model->badgeUrl;
        if (isset($model->service)) {
            $this->_service = new TrustServiceNameModel($model->service);
        }
    }

    /**
     * Gets the service's provider.
     *
     * @return string The service's provider.
     */
    public function getProvider()
    {
        return $this->_provider;
    }

    /**
     * Gets the url of the service's badge.
     *
     * @return string The url of the service's badge.
     */
    public function getBadgeUrl()
    {
        return $this->_badgeUrl;
    }

    /**
     * Gets the trust service.
     *
     * @return TrustServiceNameModel The trust service.
     */
    public function getService()
    {
        return $this->_service;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "service":
                return $this->getService();
            case "badgeUrl":
                return $this->getBadgeUrl();
            case "provider":
                return $this->getProvider();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}