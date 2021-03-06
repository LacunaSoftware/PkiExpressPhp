<?php

namespace Lacuna\PkiExpress;

/**
 * Class PdfMarkQRCodeElement
 * @package Lacuna\PkiExpress
 *
 * @property $qrCodeData mixed
 * @property $drawQuietZones bool
 */
class PdfMarkQRCodeElement extends PdfMarkElement
{
    public $qrCodeData;
    public $drawQuietZones;


    public function __construct($relativeContainer = null, $qrCodeData = null)
    {
        parent::__construct(PdfMarkElementType::QRCODE, $relativeContainer);
        $this->qrCodeData = $qrCodeData;
    }
}