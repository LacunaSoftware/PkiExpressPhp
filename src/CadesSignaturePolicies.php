<?php

namespace Lacuna\PkiExpress;

/*
 *
 */
class CadesSignaturePolicies
{
    const ICPBR_ADR_BASICA = 'ad-rb';
    const ICPBR_ADR_BASICA_WITH_REVOCATION_VALUES = 'ad-rb-rv';
    const ICPBR_ADR_TEMPO = 'ad-rt';
    const ICPBR_ADR_TEMPO_WITH_REVOCATION_VALUES = 'ad-rt-rv';
    const ICPBR_ADR_COMPLETA = 'ad-rc';
    const BES = 'cades';
    const BES_WITH_REVOCATION_VALUES = 'cades-rv';
    const CADES_T = 'cades-t';

    public static function requireTimestamp($policy)
    {
        if (empty($policy)) {
            return false;
        }

        return $policy == CadesSignaturePolicies::ICPBR_ADR_TEMPO ||
            $policy == CadesSignaturePolicies::ICPBR_ADR_TEMPO_WITH_REVOCATION_VALUES ||
            $policy == CadesSignaturePolicies::ICPBR_ADR_COMPLETA ||
            $policy == CadesSignaturePolicies::CADES_T;
    }
}