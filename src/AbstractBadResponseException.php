<?php

/**
 * Auth - An extensible authentication library for PHP
 * www.bueller.ca/auth
 *
 * AbstractBadResponseException
 * @copyright Copyright (c) 2015, Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under the BSD 2-clause license
 * www.bueller.ca/auth/license
 */

namespace MattFerris\Auth;

abstract class AbstractBadResponseException extends \Exception
{
    public function __construct(callable $callback)
    {
        $msg = 'bad response returned by ';
        if (is_array($callback)) {
            if (is_object($callback[0])) {
                $msg += get_class($callback[0]);
            } else {
                $msg += $callback[0];
            }

            $msg += '::'.$callback[1];
        } else {
            $msg += '[Closure]';
        }

        parent::__construct($msg);
    }
}

