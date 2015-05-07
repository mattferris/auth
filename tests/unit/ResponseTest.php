<?php

use MattFerris\Auth\Response;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testResponse()
    {
        $attr = array('foo' => 'bar');
        $response = new Response(true, $attr);
        $this->assertTrue($response->isValid());
        $this->assertEquals($response->getAttributes(), $attr);
    }
}

