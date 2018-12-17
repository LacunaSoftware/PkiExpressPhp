<?php
/**
 * Created by PhpStorm.
 * User: IsmaelM
 * Date: 17/12/2018
 * Time: 19:47
 */

namespace Lacuna\PkiExpress;


use Throwable;

class InstallationNotFoundException extends \Exception
{

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}