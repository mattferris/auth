<?php

/**
 * Auth - An extensible authentication library for PHP
 * www.bueller.ca/auth
 *
 * Response
 * @copyright Copyright (c) 2015, Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under the BSD 2-clause license
 * www.bueller.ca/auth/license
 */

namespace MattFerris\Auth;

class Response implements ResponseInterface
{
    /**
     * @var bool
     */
    protected $valid;

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @param bool $valid
     */
    public function __construct($valid = false, array $attributes = array())
    {
        $this->valid = $valid;
        $this->attributes = $attributes;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}

