<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 */
namespace tests\Engines;

/**
 * Class TransfersEngineTest
 * @package tests\Engines
 */
class TransfersEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PutIO\API
     */
    private $api;

    /**
     *
     */
    public function setUp()
    {
        $this->api = new \PutIO\API();
        $this->api->setHTTPEngine('Test');
    }

    /**
     *
     */
    public function testListAllReturnsExpectedData()
    {
        $response = $this->api->transfers->listall();
        
        $this->assertSame(0, $response[0]['peers_getting_from_us']);
        $this->assertSame('IN_QUEUE', $response[1]['status']);
    }

    /**
     *
     */
    public function testAddTransferReturnsExpectedData()
    {
        $response = $this->api->transfers->add('http://url.com/');
        
        $this->assertSame(0, $response['peers_getting_from_us']);
        $this->assertSame(36, $response['peers_connected']);
    }

    /**
     *
     */
    public function testGetInfoReturnsExpectedData()
    {
        $response = $this->api->transfers->info(41);

        $this->assertSame(0, $response['peers_getting_from_us']);
        $this->assertSame(36, $response['peers_connected']);
    }

    /**
     *
     */
    public function testRetryTransferReturnsExpectedData()
    {
        $this->assertTrue($this->api->transfers->retry(41));
    }

    /**
     *
     */
    public function testCleanTransferReturnsExpectedData()
    {
        $this->assertTrue($this->api->transfers->clean());
    }

    /**
     *
     */
    public function testCancelTransferReturnsExpectedData()
    {
        $this->assertTrue($this->api->transfers->cancel(41));
    }
}
