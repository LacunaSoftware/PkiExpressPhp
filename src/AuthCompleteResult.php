<?php
/**
 * Created by PhpStorm.
 * User: IsmaelM
 * Date: 04/04/2018
 * Time: 12:45
 */

namespace Lacuna\PkiExpress;


class AuthCompleteResult
{
    public $certificate;
    public $validationResults;

    public function __construct($model)
    {
        $this->certificate = new PKCertificate($model->certificate);
        if ($model->validationResults != null) {
            $this->validationResults = new ValidationResults($this->validationResults);
        }
    }

}