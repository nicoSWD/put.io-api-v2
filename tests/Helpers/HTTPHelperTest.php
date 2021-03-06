<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 */
namespace tests\Helpers;

/**
 * Class HTTPHelperTest
 * @package tests\Helpers
 */
class HTTPHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PutIO\Helpers\HTTP\HTTPHelper
     */
    private $helper;

    /**
     *
     */
    public function setUp()
    {
        $this->helper = new \PutIO\Helpers\HTTP\HTTPHelper();
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

        $this->assertFalse($method->invoke($this->helper, []));
    }

    /**
     *
     */
    public function testGetResponseCodeParsesHeaderCorrectly()
    {
        $method = new \ReflectionMethod('\PutIO\Helpers\HTTP\HTTPHelper', 'getResponseCode');
        $method->setAccessible(\true);

        $this->assertSame(200, $method->invoke($this->helper, [
            'HTTP/1.1 200 OK'
        ]));

        $this->assertSame(0, $method->invoke($this->helper, []));
    }

    /**
     *
     */
    public function testGetResponseReturnsFalseForInvalidJSON()
    {
        $method = new \ReflectionMethod('\PutIO\Helpers\HTTP\HTTPHelper', 'getResponse');
        $method->setAccessible(\true);

        $this->assertFalse($method->invokeArgs($this->helper, ['{"invalid : "}', \true]));
    }

    /**
     *
     */
    public function testGetResponseReturnsSpecificValueIfKeyIsSupplied()
    {
        $method = new \ReflectionMethod('\PutIO\Helpers\HTTP\HTTPHelper', 'getResponse');
        $method->setAccessible(\true);

        $this->assertSame(
            'yay',
            $method->invokeArgs(
                $this->helper,
                ['{"valid": "json", "robots": "yay"}', \false, 'robots']
        ));

        $this->assertFalse($method->invokeArgs(
            $this->helper,
            ['{"valid": "json", "robots": "yay"}', \false, 'this key does not exist']
        ));

        $this->assertTrue($method->invokeArgs(
                $this->helper,
                ['{"status": "OK"}', \true]
        ));

        $this->assertSame(
            ['valid' => 'json', 'robots' => 'yay'],
            $method->invokeArgs(
                $this->helper,
                ['{"valid": "json", "robots": "yay"}', \false, '']
            ));
    }

    /**
     *
     */
    public function testGetMIMETypeReturnsCorrectTypeIfExtensionIsInstalled()
    {
        $method = new \ReflectionMethod('\PutIO\Helpers\HTTP\HTTPHelper', 'getMIMEType');
        $method->setAccessible(\true);

        if (function_exists('finfo_open') && @finfo_open(FILEINFO_MIME)) {
            $expected = 'text/plain';
        } else {
            $expected = 'application/octet-stream';

            $this->markTestIncomplete(
                'PHP file info extension is missing.'
            );
        }

        $this->assertSame($expected, $method->invoke($this->helper, 'tests/data/account_info.json'));
    }

    /**
     *
     */
    public function testJSONDecodePEARReturnsExpectedData()
    {
        $method = new \ReflectionMethod('\PutIO\Helpers\HTTP\HTTPHelper', 'pearJsonDecode');
        $method->setAccessible(\true);

        $this->assertSame(['status' => 'OK'], $method->invoke($this->helper, '{"status":"OK"}'));
    }
}
