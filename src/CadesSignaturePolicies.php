<?php

namespace Lacuna\PkiExpress;

/*
 *
 */
class CadesSignaturePolicies
{
    const CADES_ICPBR_ADR_BASICA = 'ad-rb';
    const CADES_ICPBR_ADR_BASICA_WITH_REVOCATION_VALUES = 'ad-rb-rv';
    const CADES_ICPBR_ADR_TEMPO = 'ad-rt';
    const CADES_ICPBR_ADR_COMPLETA = 'ad-rc';
    const CADES_BES = 'cades';
    const CADES_BES_WITH_REVOCATION_VALUES = 'cades-rv';
    const CADES_T = 'cades-t';

    public static function requireTimestamp($policy)
    {
        if (empty($policy)) {
            return false;
        }

        return $policy == CadesSignaturePolicies::CADES_ICPBR_ADR_TEMPO ||
            $policy == CadesSignaturePolicies::CADES_ICPBR_ADR_COMPLETA ||
            $policy == CadesSignaturePolicies::CADES_T;
    }
}