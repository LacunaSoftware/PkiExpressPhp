<?php

namespace Lacuna\PkiExpress;


class AuthCompleteResult
{
    public $certificate;
    public $validationResults;


    public function __construct($model)
    {

        if ($model->certificate != null) {
            $this->certificate = new PKCertificate($model->certificate);
        }

        if ($model->validationResults != null) {
            $this->validationResults = new ValidationResults($model->validationResults);
        }
    }
}