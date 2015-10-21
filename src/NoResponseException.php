<?php

/**
 * Auth - An extensible authentication library for PHP
 * www.bueller.ca/auth
 *
 * NoResponseException
 * @copyright Copyright (c) 2015, Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under the BSD 2-clause license
 * www.bueller.ca/auth/license
 */

namespace MattFerris\Auth;

class NoResponseException extends Exception
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface
     */
    public function __construct($request)
    {
        parent::__construct('No response returned for '.get_class($request));
        $this->request = $request;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}

