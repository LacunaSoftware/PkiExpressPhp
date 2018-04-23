<?php

namespace Lacuna\PkiExpress;

/**
 * Class XmlSignaturePolicies
 * @package Lacuna\PkiExpress
 */
class XmlSignaturePolicies
{
    const BASIC = 'basic';
    const XADES_BES = 'xades';
    const NFE = 'nfe';
    const ICPBR_ADR_BASICA = 'ad-rb';
    const ICPBR_ADR_TEMPO = 'ad-rt';

    public static function requireTimestamp($policy)
    {
        if (empty($policy)) {
            return false;
        }

        return $policy == XmlSignaturePolicies::ICPBR_ADR_TEMPO;
    }
}