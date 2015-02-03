<?php

use PutIO\Helpers\HTTP;

class HTTPHelperTest extends \PHPUnit_Framework_TestCase
{
    private $helper;

    public function setup()
    {
        $this->helper = new HTTP\HTTPHelper();
    }

    public function testGetStatusReturnsCorrectValue()
    {
        $method = new ReflectionMethod('\PutIO\Helpers\HTTP\HTTPHelper', 'getStatus');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->helper, [
            'status' => 'OK'
        ]));
    }

    public function testGetReponseCodeParsesHeaderCorrectly()
    {
        $method = new ReflectionMethod('\PutIO\Helpers\HTTP\HTTPHelper', 'getResponseCode');
        $method->setAccessible(true);

        $this->assertEquals(200, $method->invoke($this->helper, [
            'HTTP/1.1 200 OK'
        ]));
    }
}
