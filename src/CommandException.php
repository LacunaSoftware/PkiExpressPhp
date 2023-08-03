<?php

namespace Lacuna\PkiExpress;

class CommandException extends \Exception
{
    public $returnCode;
    public $cmd;

    public function __construct($returnCode, $cmd, $message, \Exception $previous = null)
    {
        $this->returnCode = $returnCode;
        $this->cmd = $cmd;
        parent::__construct("The command failed with code $returnCode: $message.\n Executed command: $cmd", 0, $previous);
    }
}
