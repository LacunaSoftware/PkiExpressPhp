<?php

namespace Lacuna\PkiExpress;


class PkiBrazilCertificateFields
{
    public $_certificateType;
    public $_cpf;
    public $_cpfFormatted;
    public $_cnpj;
    public $_cnpjFormatted;
    public $_responsavel;
    public $_dateOfBirth;
    public $_companyName;
    public $_rgNumero;
    public $_rgEmissor;
    public $_rgEmissorUF;
    public $_oabNumero;
    public $_oabUF;

    public function __construct($model)
    {
        $this->_certificateType = $model->certificateType;
        $this->_cpf = $model->cpf;
        $this->_cnpj = $model->cnpj;
        $this->_responsavel = $model->responsavel;
        $this->_dateOfBirth = $model->dateOfBirth;
        $this->_companyName = $model->companyName;
        $this->_rgNumero = $model->rgNumero;
        $this->_rgEmissorUF = $model->rgEmissorUF;
        $this->_oabNumero = $model->oabNumero;
        $this->_oabUF = $model->oabUF;

        if (empty($model->cpf)) {
            $this->_cpfFormatted = "";
        } else {
            if (!preg_match("/^\d{11}$/", $model->cpf)) {
                $this->_cpfFormatted = $model->cpf;
            } else {
                $this->_cpfFormatted = sprintf("%s.%s.%s-%s", substr($model->cpf, 0, 3), substr($model->cpf, 3, 3),
                    substr($model->cpf, 6, 3), substr($model->cpf, 9));
            }
        }

        if (empty($model->cnpj)) {
            $this->_cnpjFormatted = "";
        } else {
            if (!preg_match("/^\d{14}$/", $model->cnpj)) {
                $this->_cnpjFormatted = $model->cnpj;
            } else {
                $this->_cnpjFormatted = sprintf("%s.%s.%s/%s-%s", substr($model->cpf, 0, 2), substr($model->cpf, 2, 3),
                    substr($model->cpf, 5, 3), substr($model->cpf, 8, 4), substr($model->cpf, 12));
            }
        }

    }

    public function getCertificateType()
    {
        return $this->_certificateType;
    }

    public function getCpf()
    {
        return $this->_cpf;
    }

    public function getCpfFormatted()
    {
        return $this->_cpfFormatted;
    }

    public function getCnpj()
    {
        return $this->_cnpj;
    }

    public function getCnpjFormatted()
    {
        return $this->_cnpjFormatted;
    }

    public function getResponsavel()
    {
        return $this->_responsavel;
    }

    public function getDateOfBirth()
    {
        return $this->_dateOfBirth;
    }

    public function getCompanyName()
    {
        return $this->_companyName;
    }

    public function getRgNumero()
    {
        return $this->_rgNumero;
    }

    public function getRgEmissor()
    {
        return $this->_rgEmissor;
    }

    public function getRgEmissorUF()
    {
        return $this->_rgEmissorUF;
    }

    public function getOabNumero()
    {
        return $this->_oabNumero;
    }

    public function getOabUF()
    {
        return $this->_oabUF;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "certificateType":
                return $this->getCertificateType();
            case "cpf":
                return $this->getCpf();
            case "cpfFormatted":
                return $this->getCpfFormatted();
            case "cnpj":
                return $this->getCnpj();
            case "cnpjFormatted":
                return $this->getCnpjFormatted();
            case "responsavel":
                return $this->getResponsavel();
            case "dateOfBirth":
                return $this->getDateOfBirth();
            case "companyName":
                return $this->getCompanyName();
            case "rgNumero":
                return $this->getRgNumero();
            case "rgEmissor":
                return $this->getRgEmissor();
            case "rgEmissorUF":
                return $this->getRgEmissorUF();
            case "oabNumero":
                return $this->getOabNumero();
            case "oabUF":
                return $this->getOabUF();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }

}