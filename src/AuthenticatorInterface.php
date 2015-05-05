<?php

namespace MattFerris\Auth;

interface AuthenticatorInterface
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function authenticate(RequestInterface $request);
}
