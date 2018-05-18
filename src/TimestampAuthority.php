<?php

namespace Lacuna\PkiExpress;

/**
 * Class TimestampAuthority
 * @package Lacuna\PkiExpress
 *
 * @property-read $url string
 * @property-read $token string
 * @property-read $sslThumbprint string
 * @property-read $basicAuth string
 * @property-read $authType string
 */
class TimestampAuthority
{
    private $_url;
    private $_token;
    private $_sslThumbprint;
    private $_basicAuth;
    private $_authType;


    /** @private */
    const NONE = 0;

    /** @private */
    const BASIC_AUTH = 1;

    /** @private */
    const SSL = 2;

    /** @private */
    const OAUTH_TOKEN = 3;


    public function __construct($url)
    {
        $this->_url = $url;
        $this->_authType = TimestampAuthority::NONE;
    }

    public function setOAuthTokenAuthentication($token)
    {
        $this->_token = $token;
        $this->_authType = TimestampAuthority::OAUTH_TOKEN;
    }

    public function setBasicAuthentication($username, $password)
    {
        $this->_basicAuth = "{$username}:{$password}";
        $this->_authType = TimestampAuthority::BASIC_AUTH;
    }

    public function setSSLAuthentication($sslThumbprint)
    {
        $this->_sslThumbprint = $sslThumbprint;
        $this->_authType = TimestampAuthority::SSL;
    }

    /**
     * Gets the timestamp authority's address.
     *
     * @return string The timestamp authority's address.
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Gets the OAuth token used on authentication.
     *
     * @return string The OAuth token used on authentication.
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Gets the client certificate's thumbprint used on SSL authentication.
     *
     * @return string The client certificate's thumbprint used on SSL authentication.
     */
    public function getSSLThumbprint()
    {
        return $this->_sslThumbprint;
    }

    /**
     * Gets the parameters "username" and "password" for basic authentication.
     *
     * @return string The parameters "username" and "password" for basic authentication.
     */
    public function getBasicAuth()
    {
        return $this->_basicAuth;
    }

    /**
     * Gets the authentication's type.
     *
     * @return string The authentication's type.
     */
    public function getAuthType()
    {
        return $this->_authType;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "url":
                return $this->getUrl();
            case "token":
                return $this->getToken();
            case "sslThumbprint":
                return $this->getSSLThumbprint();
            case "basicAuth":
                return $this->getBasicAuth();
            case "authType":
                return $this->getAuthType();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . "::$" . $prop);
                return null;
        }
    }

    function addCmdArguments(&$args) {

        array_push($args, '--tsa-url');
        array_push($args, $this->_url);

        // User choose SSL authentication.
        switch ($this->_authType) {
            case TimestampAuthority::NONE:
                break;
            case TimestampAuthority::BASIC_AUTH:
                array_push($args, '--tsa-basic-auth');
                array_push($args, $this->_basicAuth);
                break;
            case TimestampAuthority::SSL:
                array_push($args, '--tsa-ssl-thumbprint');
                array_push($args, $this->_sslThumbprint);
                break;
            case TimestampAuthority::OAUTH_TOKEN:
                array_push($args, '--tsa-token');
                array_push($args, $this->_token);
                break;
            default:
                throw new \RuntimeException('Unknown authentication type of the timestamp authority');

        }
    }
}