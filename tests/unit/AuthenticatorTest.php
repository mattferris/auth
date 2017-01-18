<?php

use MattFerris\Auth\Authenticator;
use MattFerris\Auth\RequestInterface;

class AuthenticatorTest extends PHPUnit_Framework_TestCase
{
    public function testProvides()
    {
        $provider = $this->getMockBuilder('MattFerris\Provider\ProviderInterface')
            ->setMethods(array('provides', 'doFooAuth', 'manipulateFoo'))
            ->getMock();

        $provider->expects($this->once())
            ->method('provides')
            ->with($this->isInstanceOf('MattFerris\Auth\Authenticator'));

        $auth = new Authenticator();
        $auth->register($provider);
    }

    public function testAuthenticate()
    {
        $requestA = $this->getMockBuilder('MattFerris\Auth\RequestInterface')
            ->setMockClassName('FooRequest')
            ->getMock();

        $requestB = $this->getMockBuilder('MattFerris\Auth\RequestInterface')
            ->setMockClassName('BarRequest')
            ->getMock();

        $response = $this->getMockBuilder('MattFerris\Auth\ResponseInterface')
            ->setMethods(array('isValid', 'getAttributes'))
            ->getMock();

        $auth = new Authenticator();

        $auth->handle('FooRequest', function (RequestInterface $request) use ($response) {
            return $response;
        });

        $resp = $auth->authenticate($requestA);
        $this->assertInstanceOf('MattFerris\Auth\ResponseInterface', $resp);
    }

    public function testManipulation()
    {
        $request = $this->getMockBuilder('MattFerris\Auth\RequestInterface')
            ->setMockClassName('FooRequest')
            ->getMock();

        $fooResponse = $this->getMockBuilder('MattFerris\Auth\ResponseInterface')
            ->setMockClassName('FooResponse')
            ->setMethods(array('isValid', 'getAttributes'))
            ->getMock();

        $barResponse = $this->getMockBuilder('MattFerris\Auth\ResponseInterface')
            ->setMockClassName('BarResponse')
            ->setMethods(array('isValid', 'getAttributes'))
            ->getMock();

        $auth = new Authenticator();

        $auth->handle('FooRequest', function (FooRequest $request) use ($fooResponse) {
            return $fooResponse;
        });

        $auth->manipulate('FooResponse', function (FooResponse $response) use ($barResponse) {
            return $barResponse;
        });

        $resp = $auth->authenticate($request);
        $this->assertInstanceOf('BarResponse', $resp);
    }
}

