<?php

use MattFerris\Auth\Authenticator;
use MattFerris\Auth\RequestInterface;

class AuthenticatorTest extends PHPUnit_Framework_TestCase
{
    public function testProvides()
    {
        $provider = $this->getMockBuilder('MattFerris\Auth\ProviderInterface')
            ->setMethods(array('provides', 'doFooAuth', 'manipulateFoo'))
            ->getMock();

        $provider->expects($this->once())
            ->method('provides')
            ->willReturn(array(
                'handlers' => array(
                    'FooRequest' => array($provider, 'doFooAuth'),
                    'BarRequest' => function(){}
                ),
                'manipulators' => array(
                    'FooResponse' => array($provider, 'manipulateFoo'),
                    'BarResponse' => function(){}
                )
            ));

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

        $provider = $this->getMockBuilder('MattFerris\Auth\ProviderInterface')
            ->setMethods(array('provides', 'doFooAuth'))
            ->getMock();

        $provider->expects($this->once())
            ->method('provides')
            ->willReturn(array(
                'handlers' => array(
                    'FooRequest' => array($provider, 'doFooAuth'),
                    'BarRequest' => function (BarRequest $request) use ($response) {
                        return $response;
                    }
                )
            ));

        $provider->expects($this->once())
            ->method('doFooAuth')
            ->with($requestA)
            ->willReturn($response);

        $auth = new Authenticator();
        $auth->register($provider);

        $resp = $auth->authenticate($requestA);
        $this->assertInstanceOf('MattFerris\Auth\ResponseInterface', $resp);

        $resp = $auth->authenticate($requestB);
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

        $provider = $this->getMockBuilder('MattFerris\Auth\ProviderInterface')
            ->setMethods(array('provides'))
            ->getMock();

        $provider->expects($this->once())
            ->method('provides')
            ->willReturn(array(
                'handlers' => array(
                    'FooRequest' => function (FooRequest $request) use ($fooResponse) {
                        return $fooResponse;
                    }
                ),
                'manipulators' => array(
                    'FooResponse' => function (FooResponse $response) use ($barResponse) {
                        return $barResponse;
                    }
                )
            ));

        $auth = new Authenticator();
        $auth->register($provider);
        $resp = $auth->authenticate($request);
        $this->assertInstanceOf('BarResponse', $resp);
    }
}

