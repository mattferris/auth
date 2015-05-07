<?php

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

