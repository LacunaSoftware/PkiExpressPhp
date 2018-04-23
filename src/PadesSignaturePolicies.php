<?php

namespace Lacuna\PkiExpress;

/**
 * Class PadesSignaturePolicies
 * @package Lacuna\PkiExpress
 */
class PadesSignaturePolicies
{
    const BASIC = 'pades';
    const PADES_T = 'pades-t';

    public static function requireTimestamp($policy)
    {
        if (empty($policy)) {
            return false;
        }

        return $policy == PadesSignaturePolicies::PADES_T;
    }
}