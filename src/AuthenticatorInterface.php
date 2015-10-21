<?php

/**
 * Auth - An extensible authentication library for PHP
 * www.bueller.ca/auth
 *
 * AuthenticatorInterface
 * @copyright Copyright (c) 2015, Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under the BSD 2-clause license
 * www.bueller.ca/auth/license
 */

namespace MattFerris\Auth;

interface AuthenticatorInterface
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function authenticate(RequestInterface $request);
}
