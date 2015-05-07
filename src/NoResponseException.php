<?php

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

