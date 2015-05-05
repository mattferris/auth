<?php

namespace MattFerris\Auth;

class Authenticator implements AuthenticatorInterface
{
    /**
     * @var array
     */
    protected $providers = array();

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function authenticate(RequestInterface $request)
    {
        $type = get_class($request);

        if (!isset($this->providers[$type])) {
            throw new ProviderNotFoundException($type);
        }

        return call_user_func($this->providers[$type], $request);
    }

    /**
     * @param ProviderInterface $provider
     * @return Authenticator
     */
    public function register(ProviderInterface $provider)
    {
        $provides = $provider->provides();
        foreach ($provides as $type => $method) {
            $this->providers[$type] = array($provider, $method);
        }
        return $this;
    }
}

