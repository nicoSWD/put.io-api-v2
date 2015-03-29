<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 */
declare(strict_types=1);

namespace {
    if (!class_exists('CURLFile')) {
        class CURLFile
        {
            public function __construct(string $filename, string $mimetype = '', string $postname = '')
            {

            }
        }

        define('CURLOPT_SAFE_UPLOAD', -1);
    }
}

namespace tests\Engines {

/**
 * Class CurlEngineTest
 * @package tests\Engines
 */
class CurlEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PutIO\Engines\HTTP\CurlEngine
     */
    private $engine;

    /**
     *
     */
    public function setUp()
    {
        $this->engine = new \PutIO\Engines\HTTP\CurlEngine();
    }

    /**
     *
     */
    public function testGetDefaultOptionsReturnsExpectedData()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\CurlEngine', 'getDefaultOptions');
        $method->setAccessible(true);

        $options = $method->invoke($this->engine);

        $this->assertArrayHasKey(CURLOPT_USERAGENT, $options);
        $this->assertArrayHasKey(CURLOPT_HTTPHEADER, $options);
        $this->assertArrayHasKey(CURLOPT_CAINFO, $options); // Verify by default

        $property = new \ReflectionProperty('\PutIO\Engines\HTTP\CurlEngine', 'verifyPeer');
        $property->setAccessible(true);
        $property->setValue($this->engine, false);

        $options = $method->invoke($this->engine);

        $this->assertArrayNotHasKey(CURLOPT_CAINFO, $options);
    }

    /**
     *
     */
    public function testPostReturnsExpectedOptions()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\CurlEngine', 'post');
        $method->setAccessible(true);

        $file = tempnam(sys_get_temp_dir(), (string) time());

        if (!$file || @!touch($file)) {
            $this->markTestSkipped(
                'Unable to create temp file. Test skipped.'
            );
        } else {
            $params = ['file' => '@' . $file];

            $options = $method->invoke($this->engine, $params);
            $this->assertArrayHasKey(CURLOPT_POST, $options);
            $this->assertArrayHasKey(CURLOPT_SAFE_UPLOAD, $options);
        }
    }

    /**
     * @expectedException \PutIO\Exceptions\RemoteConnectionException
     */
    public function testHandleResponseThrowsExceptionCurlErrNum()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\CurlEngine', 'handleResponse');
        $method->setAccessible(true);

        $params = [404, 'Not Found', 404];
        $method->invokeArgs($this->engine, $params);
    }

    /**
     * @expectedException \PutIO\Exceptions\RemoteConnectionException
     */
    public function testHandleResponseThrowsExceptionOnBadResponseCode()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\CurlEngine', 'handleResponse');
        $method->setAccessible(true);

        $params = [0, 'Not Found', 400];
        $method->invokeArgs($this->engine, $params);
    }

    /**
     *
     */
    public function testConfigureRequestOptionsReturnsExpectedData()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\CurlEngine', 'configureRequestOptions');
        $method->setAccessible(true);

        $params = ['account/info', 'POST', [], ''];
        list($url, $options) = $method->invokeArgs($this->engine, $params);

        $this->assertSame('https://api.put.io/v2/account/info', $url);
        $this->assertTrue($options[CURLOPT_RETURNTRANSFER]);

        $params = ['account/info', 'GET', ['foo' => 'bar'], ''];
        list($url,) = $method->invokeArgs($this->engine, $params);

        $this->assertSame('https://api.put.io/v2/account/info?foo=bar', $url);

        $tmpName = tempnam(sys_get_temp_dir(), (string) time());

        $params = ['account/info', 'GET', ['foo' => 'bar'], $tmpName];
        list($url, $options) = $method->invokeArgs($this->engine, $params);

        $this->assertInternalType('resource', $options[CURLOPT_FILE]);
        $this->assertSame('https://api.put.io/v2/account/info?foo=bar', $url);

        $file = tempnam(sys_get_temp_dir(), (string) time());
        
        if (!$file || @!touch($file)) {
            $this->markTestIncomplete(
                'Unable to create temp file. Test incomplete.'
            );
        } else {
            $params = ['account/info', 'POST', ['oauth_token' => '123', 'file' => '@' . $file], ''];
            list($url,) = $method->invokeArgs($this->engine, $params);

            $this->assertSame('https://upload.put.io/v2/account/info?oauth_token=123', $url);
        }
    }

    /**
     * @expectedException \PutIO\Exceptions\LocalStorageException
     */
    public function testConfigureRequestOptionsThrowsExceptionOnOutFileError()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\CurlEngine', 'configureRequestOptions');
        $method->setAccessible(true);
        $tmpName = '/\/\/\/';

        $params = ['account/info', 'GET', [], $tmpName];
        $method->invokeArgs($this->engine, $params);
    }
}}
