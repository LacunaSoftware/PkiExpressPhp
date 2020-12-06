<?php


namespace Lacuna\PkiExpress;


class Pkcs12Certificate
{
    public $content;

    public function __construct($pfx)
    {
        $this->content = base64_decode($pfx);
    }

    public function getContentRaw()
    {
        return $this->content;
    }

    public function setContentRaw($contentRaw)
    {
        $this->content = $contentRaw;
    }

    public function getContentBase64()
    {
        if (!isset($this->content)) {
            return null;
        }
        return base64_encode($this->content);
    }

    public function setContentBase64($contentBase64)
    {
        if (!($raw = base64_encode($contentBase64))) {
            throw new \RuntimeException("The provided content is not Base64-encoded");
        }

        $this->setContentRaw($raw);
    }

    public function writeToFile($path)
    {
        file_put_contents($path, $this->content);
    }
}