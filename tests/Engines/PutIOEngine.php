<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * All HTTP engines must implement this interface.
 */
namespace tests\Engines;

use PutIO\Helpers\HTTP;

/**
 * Class HTTPHelperTest
 */
class HTTPHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    private $helper;

    /**
     *
     */
    public function setUp()
    {
        $this->helper = new HTTP\HTTPHelper();
    }

    /**
     *
     */
    public function testGetStatusReturnsCorrectValue()
    {
        $method = new \ReflectionMethod('\PutIO\Helpers\HTTP\HTTPHelper', 'getStatus');
        $method->setAccessible(\true);

        $this->assertTrue($method->invoke($this->helper, [
            'status' => 'OK'
        ]));
    }

    /**
     *
     */
    public function testGetReponseCodeParsesHeaderCorrectly()
    {
        $method = new \ReflectionMethod('\PutIO\Helpers\HTTP\HTTPHelper', 'getResponseCode');
        $method->setAccessible(\true);

        $this->assertEquals(200, $method->invoke($this->helper, [
            'HTTP/1.1 200 OK'
        ]));
    }
}
