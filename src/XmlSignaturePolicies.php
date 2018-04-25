<?php

namespace Lacuna\PkiExpress;

/**
 * Class XmlSignaturePolicies
 * @package Lacuna\PkiExpress
 */
class XmlSignaturePolicies
{
    // Backward compatibility
    const BASIC = 'basic';
    const NFE = 'nfe';

    const XML_BASIC = 'basic';
    const XML_XADES_BES = 'xades';
    const XML_NFE = 'nfe';
    const XML_ICPBR_ADR_BASICA = 'ad-rb';
    const XML_ICPBR_ADR_TEMPO = 'ad-rt';

    public static function requireTimestamp($policy)
    {
        if (empty($policy)) {
            return false;
        }

        return $policy == XmlSignaturePolicies::XML_ICPBR_ADR_TEMPO;
    }
}