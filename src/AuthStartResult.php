<?php
/**
 * Created by PhpStorm.
 * User: IsmaelM
 * Date: 04/04/2018
 * Time: 12:47
 */

namespace Lacuna\PkiExpress;


class AuthStartResult
{
    public $nonce;
    public $digestAlgorithm;
    public $digestAlgorithmOid;

    public function __construct($model)
    {
        $this->nonce = $model->toSignData;
        $this->digestAlgorithm = $model->digestAlgorithmName;
        $this->digestAlgorithmOid = $model->digestAlgorithmOid;
    }

}