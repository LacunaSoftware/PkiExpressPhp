<?php

namespace Lacuna\PkiExpress;


class CertificateExplorerResult
{
    public $certificate;
    public $validationResults;


    public function __construct($model)
    {

        if ($model->info != null) {
            $this->certificate = new PKCertificate($model->info);
        }

        if ($model->validationResults != null) {
            $this->validationResults = new ValidationResults($model->validationResults);
        }
    }
}