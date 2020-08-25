<?php

namespace Lacuna\PkiExpress;
use DateTime;

/**
 * Class TrustServiceSessionResult
 * @package Lacuna\PkiExpress
 */
class TrustServiceSessionResult
{
    private $_session;
    private $_customState;
    private $_service;
    private $_sessionType;
    private $_expiresOn;

    public function __construct($model)
    {
        $this->_session = $model->session;
        $this->_customState = $model->customState;
        $this->_sessionType = $model->type;

        if (isset($model->service)) {
            $this->_service = new TrustServiceNameModel($model->service);
        }

        if (isset($model->expiresOn)) {
            $this->_expiresOn = new DateTime($model->expiresOn);
        }
    }

    /**
     * Gets the session.
     *
     * @return string The session.
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Gets the custom state.
     *
     * @return string The custom state.
     */
    public function getCustomState()
    {
        return $this->_customState;
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

    /**
     * Gets the session type.
     *
     * @return string The session type.
     */
    public function getSessionType()
    {
        return $this->_sessionType;
    }

    /**
     * Gets the expiration date.
     *
     * @return DateTime The expiration date.
     */
    public function getExpiresOn()
    {
        return $this->_expiresOn;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "service":
                return $this->getService();
            case "customState":
                return $this->getCustomState();
            case "session":
                return $this->getSession();
            case "sessionType":
                return $this->getSessionType();
            case "expiresOn":
                return $this->getExpiresOn();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}