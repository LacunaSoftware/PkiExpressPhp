<?php

namespace Lacuna\PkiExpress;

/**
 * Class SignatureStarter
 * @package Lacuna\PkiExpress
 *
 * @property-write $signaturePolicy string
 * @property $timestampAuthority TimestampAuthority
 */
abstract class SignatureStarter extends PkiExpressOperator
{
    protected $certificatePath;

    protected $_signaturePolicy;


    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    //region setCertificate

    /**
     * Sets the signer certificate's local path.
     *
     * @param $path string The path to the signer certificate.
     * @throws \Exception If the provided path is not found.
     */
    public function setCertificateFromPath($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided certificate was not found");
        }

        $this->certificatePath = $path;
    }

    /**
     * Sets the signer certificate's binary content.
     *
     * @param $contentRaw string The content of the signer certificate.
     */
    public function setCertificateFromContentRaw($contentRaw)
    {
        $tempFilePath = parent::createTempFile();
        file_put_contents($tempFilePath, $contentRaw);
        $this->certificatePath = $tempFilePath;
    }

    /**
     * Sets the signer certificate's Base64-encoded content.
     *
     * @param $contentBase64 string The Base64-encoded content of the signer certificate.
     * @throws \Exception If the provided parameter is not a Base64 string.
     */
    public function setCertificateFromContentBase64($contentBase64)
    {
        if (!($raw = base64_decode($contentBase64))) {
            throw new \Exception("The provided certificate is not Base64-encoded");
        }

        $this->setCertificateFromContentRaw($raw);
    }

    /**
     * Sets the signer certificate's local path. This method is only an alias for the setCertificateFromPath() method.
     *
     * @param $path string The path of the signer certificate.
     * @throws \Exception If the provided path is not found.
     */
    public function setCertificate($path)
    {
        $this->setCertificateFromPath($path);
    }

    /**
     * Sets the signer certificate's binary content. This method is only an alias for the
     * setCertificateFromContentRaw() method.
     *
     * @param $contentRaw string The content of the signer certificate.
     */
    public function setCertificateContent($contentRaw)
    {
        $this->setCertificateFromContentRaw($contentRaw);
    }

    /**
     * Sets the signer certificate's Base64-encoded content. This methos is only an alias for the
     * setCertificateFromContentBase64() method.
     *
     * @param $contentBase64 string The Base64-encoded content of the signer certificate.
     * @throws \Exception If the provided parameter is not Base64 string.
     */
    public function setCertificateBase64($contentBase64)
    {
        $this->setCertificateFromContentBase64($contentBase64);
    }

    //endregion

    protected function getResult($response, $transferFile)
    {
        return (object)array(
            "toSignHash" => $response[0],
            "digestAlgorithm" => $response[1],
            "digestAlgorithmOid" => $response[2],
            "transferFile" => $transferFile
        );
    }

    protected function verifyAndAddCommonOptions(&$args) {

        if (StandardSignaturePolicies::requireTimestamp($this->_signaturePolicy) && empty($this->_timestampAuthority)) {
            throw new \RuntimeException("The provided policy requires a timestamp authority and none was provided.");
        }

        // Set signature policy.
        if (isset($this->_signaturePolicy)) {
            $args[] = '--policy';
            $args[] = $this->_signaturePolicy;

            // This operation evolved after version 1.5 to other signature policies.
            if ($this->_signaturePolicy != StandardSignaturePolicies::XML_DSIG_BASIC &&
                $this->_signaturePolicy != StandardSignaturePolicies::NFE_PADRAO_NACIONAL) {

                // This operation can only be used on versions greater than 1.5 of the PKI Express.
                $this->versionManager->requireVersion("1.5");
            }
        }

        // Add timestamp authority.
        if (isset($this->_timestampAuthority)) {
            $this->_timestampAuthority->addCmdArguments($args);

            // This option can only be used on versions greater than 1.5 of the PKI Express.
            $this->versionManager->requireVersion("1.5");
        }
    }

    public abstract function start();
}
