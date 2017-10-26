<?php

namespace Lacuna\PkiExpress;


class SignatureStarter extends PkiExpressOperator
{
    protected $certificatePath;


    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function setCertificate($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided certificate was not found");
        }

        $this->certificatePath = $path;
    }

    public function setCertificateBase64($certBase64)
    {
        $certTempFilePath = $this->createTempFile();
        $certContent = base64_decode($certBase64);
        file_put_contents($certTempFilePath, $certContent);
        $this->certificatePath = $certTempFilePath;
    }
}