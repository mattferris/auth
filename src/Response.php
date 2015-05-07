<?php

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

