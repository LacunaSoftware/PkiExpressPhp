<?php

namespace Lacuna\PkiExpress;

/**
 * Class ValidationItem
 * @package Lacuna\PkiExpress
 *
 * @property-read $innerValidationResults ValidationResults
 * @property-read $type string
 * @property-read $message string
 * @property-read $detail string
 */
class ValidationItem
{
    private $_innerValidationResults;
    private $_type;
    private $_message;
    private $_detail;

    public function __construct($model)
    {
        $this->_type = $model->type;
        $this->_message = $model->message;
        $this->_detail = $model->detail;
        if ($model->innerValidationResults !== null) {
            $this->_innerValidationResults = new ValidationResults($model->innerValidationResults);
        }
    }

    /**
     * Gets the validation type field.
     *
     * @return string The validation type field.
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Gets the message of the validation.
     *
     * @return string The message of the validation.
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Gets the detail of the validation.
     *
     * @return string The detail of the validation.
     */
    public function getDetail()
    {
        return $this->_detail;
    }

    public function __toString()
    {
        return $this->toString(0);
    }

    public function toString($indentationLevel)
    {
        $text = '';
        $text .= $this->_message;
        if (!empty($this->_detail)) {
            $text .= " ({$this->_detail})";
        }
        if ($this->_innerValidationResults !== null) {
            $text .= "\n";
            $text .= $this->_innerValidationResults->toString($indentationLevel + 1);
        }
        return $text;
    }

    public function __get($name)
    {
        switch ($name) {
            case "innerValidationResults":
                return $this->_innerValidationResults;
            case "type":
                return $this->_type;
            case "message":
                return $this->_message;
            case "detail":
                return $this->_detail;
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name);
                return null;
        }
    }
}
