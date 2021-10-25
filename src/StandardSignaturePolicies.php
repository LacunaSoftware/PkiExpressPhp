<?php

namespace Lacuna\PkiExpress;


class StandardSignaturePolicies
{
    // CAdES policies
    const PKI_BRAZIL_CADES_ADR_BASICA = 'adrb';
    const PKI_BRAZIL_CADES_ADR_BASICA_WITH_REVOCATION_VALUES = 'adrb-rv';
    const PKI_BRAZIL_CADES_ADR_TEMPO = 'adrt';
    const PKI_BRAZIL_CADES_ADR_COMPLETA = 'adrc';
    const CADES_BES = 'cades';
    const CADES_BES_WITH_REVOCATION_VALUES = 'cades-rv';
    const CADES_T = 'cades-t';

    // PAdES policies
    const PADES_BASIC = 'pades';
    const PADES_BASIC_WITH_LTV = 'pades-ltv';
    const PADES_T = 'pades-t';

    // XML policies
    const NFE_PADRAO_NACIONAL = 'nfe';
    const XADES_BES = 'xades';
    const XML_DSIG_BASIC = 'basic';
    const PKI_BRAZIL_XML_ADR_BASICA = 'adrb';
    const PKI_BRAZIL_XML_ADR_TEMPO = 'adrt';
    const PKI_BRAZIL_XML_ADR_ARQUIVAMENTO = 'adra';
    const COD_WITH_SHA1 = 'cod-sha1';
    const COD_WITH_SHA256 = 'cod-sha256';
    const XADES_BRAZIL = 'xades-brazil';
    const PKI_BRAZIL = 'pki-brazil';
    const PKI_BRAZIL_WITH_CERT_PROTECTION = 'pki-brazil-with-cert-protection';

    public static function requireTimestamp($policy)
    {
        if (empty($policy)) {
            return false;
        }

        return $policy == StandardSignaturePolicies::PKI_BRAZIL_CADES_ADR_TEMPO ||
            $policy == StandardSignaturePolicies::PKI_BRAZIL_CADES_ADR_COMPLETA ||
            $policy == StandardSignaturePolicies::CADES_T ||
            $policy == StandardSignaturePolicies::PADES_T ||
            $policy == StandardSignaturePolicies::PKI_BRAZIL_XML_ADR_TEMPO ||
            $policy == StandardSignaturePolicies::PKI_BRAZIL_XML_ADR_ARQUIVAMENTO;
    }
}