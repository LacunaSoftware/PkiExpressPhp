<?php

namespace Lacuna\PkiExpress;


class StandardSignaturePolicies
{
    // CAdES policies
    const PKI_BRAZIL_CADES_ADR_BASICA = 'ad-rb';
    const PKI_BRAZIL_CADES_ADR_BASICA_WITH_REVOCATION_VALUES = 'ad-rb-rv';
    const PKI_BRAZIL_CADES_ADR_TEMPO = 'ad-rt';
    const PKI_BRAZIL_CADES_ADR_COMPLETA = 'ad-rc';
    const CADES_BES = 'cades';
    const CADES_BES_WITH_REVOCATION_VALUES = 'cades-rv';
    const CADES_T = 'cades-t';

    // PAdES policies
    const PADES_BASIC = 'pades';
    const PADES_T = 'pades-t';

    // XML policies
    const NFE_PADRAO_NACIONAL = 'nfe';
    const XADES_BES = 'xades';
    const XML_DSIG_BASIC = 'basic';
    const PKI_BRAZIL_XML_ADR_BASICA = 'ad-rb';
    const PKI_BRAZIL_XML_ADR_TEMPO = 'ad-rt';

    public static function requireTimestamp($policy)
    {
        if (empty($policy)) {
            return false;
        }

        return $policy == StandardSignaturePolicies::PKI_BRAZIL_CADES_ADR_TEMPO ||
            $policy == StandardSignaturePolicies::PKI_BRAZIL_CADES_ADR_COMPLETA ||
            $policy == StandardSignaturePolicies::CADES_T ||
            $policy == StandardSignaturePolicies::PADES_T ||
            $policy == StandardSignaturePolicies::PKI_BRAZIL_XML_ADR_TEMPO;
    }
}