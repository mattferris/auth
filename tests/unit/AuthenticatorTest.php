<?php

use MattFerris\Auth\Authenticator;

class AuthenticatorTest extends PHPUnit_Framework_TestCase
{
    public function testProvides()
    {
        $provider = $this->getMockBuilder('MattFerris\Auth\ProviderInterface')
            ->setMethods(array('provides', 'doFooAuth'))
            ->getMock();

        $provider->expects($this->once())
            ->method('provides')
            ->willReturn(array('FooRequest'=>'doFooAuth'));

        $auth = new Authenticator();
        $auth->register($provider);
    }

    public function testAuthenticate()
    {
        $request = $this->getMockBuilder('MattFerris\Auth\RequestInterface')
            ->setMockClassName('FooRequest')
            ->getMock();

        $response = $this->getMockBuilder('MattFerris\Auth\ResponseInterface')
            ->setMethods(array('isValid'))
            ->getMock();

        $provider = $this->getMockBuilder('MattFerris\Auth\ProviderInterface')
            ->setMethods(array('provides', 'doFooAuth'))
            ->getMock();

        $provider->expects($this->once())
            ->method('provides')
            ->willReturn(array('FooRequest'=>'doFooAuth'));

        $provider->expects($this->once())
            ->method('doFooAuth')
            ->with($request)
            ->willReturn($response);

        $auth = new Authenticator();
        $auth->register($provider);
        $resp = $auth->authenticate($request);
        $this->assertInstanceOf('MattFerris\Auth\ResponseInterface', $resp);
    }
}

