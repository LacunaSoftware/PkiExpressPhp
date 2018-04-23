<?php

namespace Lacuna\PkiExpress;


class TimestampAuthority
{
    private $url;
    private $token;
    private $sslThumbprint;
    private $basicAuth;
    private $type;

    /** @private */
    const BASIC_AUTH = 0;

    /** @private */
    const SSL = 1;

    /** @private */
    const OAUTH_TOKEN = 2;


    public function __construct($url)
    {
        $this->url = $url;
    }

    public function setOAuthTokenAuthentication($token)
    {
        $this->token = $token;
        $this->type = TimestampAuthority::OAUTH_TOKEN;
    }

    public function setBasicAuthentication($username, $password)
    {
        $this->basicAuth = "{$username}:{$password}";
        $this->type = TimestampAuthority::BASIC_AUTH;
    }

    public function setSSLAuthentication($sslThumbprint)
    {
        $this->sslThumbprint = $sslThumbprint;
        $this->type = TimestampAuthority::SSL;
    }
}