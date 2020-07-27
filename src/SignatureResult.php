<?php


namespace Lacuna\PkiExpress;


class SignatureResult
{
    public $signer;

    public function __construct($model) {
        $this->signer = new PKCertificate($model->signer);
    }
}