<?php


namespace Lacuna\PkiExpress;


class StandardSignaturePoliciesForValidation
{
    // PAdES policies
    const PADES = 'pades';
    const PADES_WITH_CERT_PROTECTION = 'pades-with-cert-protection';
    const PKI_BRAZIL = 'pki-brazil';
    const PKI_BRAZIL_WITH_CERT_PROTECTION = 'pki-brazil-with-cert-protection';
    const ADOBE_READER = 'adobe-reader';
}