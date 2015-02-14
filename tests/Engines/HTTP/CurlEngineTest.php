<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 */
namespace tests\Engines;

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
        $method->setAccessible(\true);

        $options = $method->invoke($this->engine);

        $this->assertArrayHasKey(CURLOPT_USERAGENT, $options);
        $this->assertArrayHasKey(CURLOPT_HTTPHEADER, $options);
        $this->assertArrayHasKey(CURLOPT_CAINFO, $options); // Verify by default

        $property = new \ReflectionProperty('\PutIO\Engines\HTTP\CurlEngine', 'verifyPeer');
        $property->setAccessible(\true);
        $property->setValue($this->engine, \false);

        $options = $method->invoke($this->engine);

        $this->assertArrayNotHasKey(CURLOPT_CAINFO, $options);
    }

    /**
     *
     */
    public function testPostReturnsExpectedOptions()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\CurlEngine', 'post');
        $method->setAccessible(\true);

        $params = ['file' => 'test.txt'];

        $options = $method->invoke($this->engine, $params);
        $this->assertArrayHasKey(CURLOPT_POST, $options);

        if (class_exists('\CURLFile')) {
            $this->assertArrayHasKey(CURLOPT_SAFE_UPLOAD, $options);
        } else {
            $this->markTestIncomplete(
                'Test requires PHP 5.5 and CURLFile'
            );
        }
    }
}
