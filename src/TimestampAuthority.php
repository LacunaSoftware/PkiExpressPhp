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
 * @property-read $type string
 */
class TimestampAuthority
{
    private $_url;
    private $_token;
    private $_sslThumbprint;
    private $_basicAuth;
    private $_type;

    /** @private */
    const BASIC_AUTH = 0;

    /** @private */
    const SSL = 1;

    /** @private */
    const OAUTH_TOKEN = 2;


    public function __construct($url)
    {
        $this->_url = $url;
    }

    public function setOAuthTokenAuthentication($token)
    {
        $this->_token = $token;
        $this->_type = TimestampAuthority::OAUTH_TOKEN;
    }

    public function setBasicAuthentication($username, $password)
    {
        $this->_basicAuth = "{$username}:{$password}";
        $this->_type = TimestampAuthority::BASIC_AUTH;
    }

    public function setSSLAuthentication($sslThumbprint)
    {
        $this->_sslThumbprint = $sslThumbprint;
        $this->_type = TimestampAuthority::SSL;
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
    public function getSslThumbprint()
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
    public function getType()
    {
        return $this->_type;
    }

    public function __get($prop)
    {
        switch ($prop) {
            case "url":
                return $this->getUrl();
            case "token":
                return $this->getToken();
            case "sslThumbprint":
                return $this->getSslThumbprint();
            case "basicAuth":
                return $this->getBasicAuth();
            case "type":
                return $this->getType();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . "::$" . $prop);
                return null;
        }
    }

    function getCmdArguments() {

        $args = [];
        array_push($args, '--tsa-url');
        array_push($args, $this->_url);

        // User choose SSL authentication.
        switch ($this->type) {
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

        return $args;

    }
}