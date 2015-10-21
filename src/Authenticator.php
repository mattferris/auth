<?php

/**
 * Auth - An extensible authentication library for PHP
 * www.bueller.ca/auth
 *
 * Authenticator
 * @copyright Copyright (c) 2015, Matt Ferris
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under the BSD 2-clause license
 * www.bueller.ca/auth/license
 */

namespace MattFerris\Auth;

class Authenticator implements AuthenticatorInterface
{
    /**
     * @var array
     */
    protected $providers = array();

    /**
     * @var array
     */
    protected $manipulators = array();

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function authenticate(RequestInterface $request)
    {
        $type = get_class($request);

        if (!isset($this->providers[$type])) {
            throw new NoHandlerDefinedException($type);
        }

        $response = null;
        foreach ($this->providers[$type] as $handler) {
            $response = call_user_func($handler, $request);
            if ($response === null) {
                continue;
            } elseif (!($response instanceof ResponseInterface)) {
                throw new BadHandlerResponseException($handler);
            } else {
                break;
            }
        }

        if ($response === null) {
            throw new NoResponseException($request);
        }

        $type = get_class($response);
        if (isset($this->manipulators[$type])) {
            foreach ($this->manipulators[$type] as $manipulator) {
                $manipulatedResponse = call_user_func($manipulator, $response);
                if ($manipulatedResponse === null) {
                    continue;
                } elseif (!($manipulatedResponse instanceof ResponseInterface)) {
                    throw new BadManipulatorResponseException($manipulatedResponse);
                } else {
                    $response = $manipulatedResponse;
                    break;
                }
            }
        }

        return $response;
    }

    /**
     * @param ProviderInterface $provider
     * @return Authenticator
     */
    public function register(ProviderInterface $provider)
    {
        $provides = $provider->provides();

        if (isset($provides['handlers'])) {
            foreach ($provides['handlers'] as $request => $handler) {
                $this->handle($request, $handler);
            }
        }

        if (isset($provides['manipulators'])) {
            foreach ($provides['manipulators'] as $response => $manipulator) {
                $this->manipulate($response, $manipulator);
            }
        }

        return $this;
    }

    /**
     * @param string $request
     * @param callable $handler
     * @return $this
     */
    public function handle($request, callable $handler)
    {
        if (!isset($this->providers[$request])) {
            $this->providers[$request] = array();
        }

        $this->providers[$request][] = $handler;

        return $this;
    }

    /**
     * @param string $response
     * @param callable $manipulator
     * @return $this
     */
    public function manipulate($response, callable $manipulator)
    {
        if (!isset($this->manipulators[$response])) {
            $this->manipulators[$response] = array();
        }

        $this->manipulators[$response][] = $manipulator;

        return $this;
    }
}

