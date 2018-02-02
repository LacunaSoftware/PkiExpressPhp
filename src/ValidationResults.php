<?php

namespace Lacuna\PkiExpress;

/**
 * Class ValidationResults
 * @package Lacuna\PkiExpress
 *
 * @property-read $errors ValidationItem[]
 * @property-read $warnings ValidationItem[]
 * @property-read $passedChecks ValidationItem[]
 */
class ValidationResults
{
    private $_errors;
    private $_warnings;
    private $_passedChecks;

    public function __construct($model)
    {
        $this->_errors = self::convertItems($model->errors);
        $this->_warnings = self::convertItems($model->warnings);
        $this->_passedChecks = self::convertItems($model->passedChecks);
    }

    public function isValid()
    {
        return empty($this->errors);
    }

    public function getChecksPerformed()
    {
        return count($this->_errors) + count($this->_warnings) + count($this->_passedChecks);
    }

    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    public function hasWarnings()
    {
        return !empty($this->_warnings);
    }

    public function __toString()
    {
        return $this->toString(0);
    }

    public function toString($indentationLevel)
    {
        $tab = str_repeat("\t", $indentationLevel);
        $text = '';
        $text .= $this->getSummary($indentationLevel);
        if ($this->hasErrors()) {
            $text .= "\n{$tab}Errors:\n";
            $text .= self::joinItems($this->_errors, $indentationLevel);
        }
        if ($this->hasWarnings()) {
            $text .= "\n{$tab}Warnings:\n";
            $text .= self::joinItems($this->_warnings, $indentationLevel);
        }
        if (!empty($this->_passedChecks)) {
            $text .= "\n{$tab}Passed checks:\n";
            $text .= self::joinItems($this->_passedChecks, $indentationLevel);
        }
        return $text;
    }

    public function getSummary($indentationLevel = 0)
    {
        $tab = str_repeat("\t", $indentationLevel);
        $text = "{$tab}Validation results: ";
        if ($this->getChecksPerformed() === 0) {
            $text .= 'no checks performed';
        } else {
            $text .= "{$this->getChecksPerformed()} checks performed";
            if ($this->hasErrors()) {
                $text .= ', ' . count($this->_errors) . ' errors';
            }
            if ($this->hasWarnings()) {
                $text .= ', ' . count($this->_warnings) . ' warnings';
            }
            if (!empty($this->_passedChecks)) {
                if (!$this->hasErrors() && !$this->hasWarnings()) {
                    $text .= ", all passed";
                } else {
                    $text .= ', ' . count($this->_passedChecks) . ' passed';
                }
            }
        }
        return $text;
    }

    private static function convertItems($items)
    {
        $converted = array();
        foreach ($items as $item) {
            $converted[] = new ValidationItem($item);
        }
        return $converted;
    }

    private static function joinItems($items, $indentationLevel)
    {
        $text = '';
        $isFirst = true;
        $tab = str_repeat("\t", $indentationLevel);
        foreach ($items as $item) {
            /** @var ValidationItem $item */
            if ($isFirst) {
                $isFirst = false;
            } else {
                $text .= "\n";
            }
            $text .= "{$tab}- ";
            $text .= $item->toString($indentationLevel);
        }
        return $text;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getWarnings()
    {
        return $this->_warnings;
    }

    public function getPassedChecks()
    {
        return $this->_passedChecks;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "errors":
                return $this->getErrors();
            case "warnings":
                return $this->getWarnings();
            case "passedChecks":
                return $this->getPassedChecks();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }
}
