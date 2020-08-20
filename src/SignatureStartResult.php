<?php


namespace Lacuna\PkiExpress;


class SignatureStartResult
{
    public $toSignHash;
    public $digestAlgorithmName;
    public $digestAlgorithmOid;
    public $transferFileId;

    public function __construct($toSignHashResult, $transferFile) {
        $this->toSignHash = $toSignHashResult->toSignHash;
        $this->digestAlgorithmName = $toSignHashResult->digestAlgorithmName;
        $this->digestAlgorithmOid = $toSignHashResult->digestAlgorithmOid;
        $this->transferFileId = $transferFile;
    }
}