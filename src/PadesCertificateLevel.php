<?php


namespace Lacuna\PkiExpress;


class PadesCertificateLevel
{
    const NOT_CERTIFIED = 'not-certified';
    const CERTIFIED_FORM_FILLING = 'certified-form-filling';
    const CERTIFIED_FORM_FILLING_AND_ANNOTATIONS = 'certified-form-filling-annotations';
    const CERTIFIED_NO_CHANGES_ALLOWED = 'certified-no-changes-allowed';
}