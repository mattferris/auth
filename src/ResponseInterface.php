<?php

namespace MattFerris\Auth;

interface ResponseInterface
{
    /**
     * @return bool
     */
    public function isValid();
}

