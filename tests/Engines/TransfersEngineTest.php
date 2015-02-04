<?php

namespace tests\Engines;

class TransfersEngineTest extends \PHPUnit_Framework_TestCase
{
    private $api;

    public function setUp()
    {
        $this->api = new \PutIO\API();
        $this->api->setHTTPEngine('Test');
    }

    public function testListAllReturnsExpectedData()
    {
        $response = $this->api->transfers->listall();
        
        $this->assertSame(0, $response[0]['peers_getting_from_us']);
        $this->assertSame('IN_QUEUE', $response[1]['status']);
    }

    public function testAddTransferReturnsExpectedData()
    {
        $response = $this->api->transfers->add('http://url.com/');
        
        $this->assertSame(0, $response['peers_getting_from_us']);
        $this->assertSame(36, $response['peers_connected']);
    }

    public function testGetInfoReturnsExpectedData()
    {
        $response = $this->api->transfers->info(41);

        $this->assertSame(0, $response['peers_getting_from_us']);
        $this->assertSame(36, $response['peers_connected']);
    }

    public function testCancelTransferReturnsExpectedData()
    {
        $this->assertTrue($this->api->transfers->retry(41));
    }

    public function testCleanTransferReturnsExpectedData()
    {
        $this->assertTrue($this->api->transfers->clean());
    }
}
