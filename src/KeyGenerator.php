<?php


namespace Lacuna\PkiExpress;


class KeyGenerator extends PkiExpressOperator
{
    public $keySize;
    public $keyFormat;
    public $genCsr;

    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    public function generate($keyFormat = null)
    {
        if (!isset($keyFormat)) {
            $keyFormat = $this->keyFormat;
        }

        $args = [];
        if (isset($this->keySize)) {
            if ($this->keySize != SupportedKeySizes::S1024
                && $this->keySize != SupportedKeySizes::S2048
                && $this->keySize != SupportedKeySizes::S4096) {
                throw new \RuntimeException("Unsupported key size: {$this->keySize}");
            }
            array_push($args, '--size');
            array_push($args, $this->keySize);
        }

        if (isset($keyFormat)) {
            array_push($args, '--format');
            array_push($args, $keyFormat);
        }

        if (isset($this->genCsr)) {
            array_push($args, '--gen-csr');
        }

        // This operation can only be used on version greater then 1.11 of the
        // PKI Express.
        $this->versionManager->requireVersion('1.11');

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_GEN_KEY, $args);

        // Parse output.
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response.
        $result = new KeyGenerationResult($parsedOutput);

        return $result;
    }
}