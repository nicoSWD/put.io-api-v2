<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 */
namespace tests\Engines;

/**
 * Class FriendsEngineTest
 * @package tests\Engines
 */
class FriendsEngineTest extends \PHPUnit_Framework_TestCase
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
        $response = $this->api->friends->listall();
        
        $this->assertSame('foo', $response[0]['name']);
        $this->assertSame('bar', $response[1]['name']);
    }

    /**
     *
     */
    public function testPendingRequestsReturnsExpectedData()
    {
        $response = $this->api->friends->pendingRequests();
        
        $this->assertSame('foo', $response[0]['name']);
        $this->assertSame('bar', $response[1]['name']);
    }

    /**
     *
     */
    public function testSendRequestReturnsExpectedData()
    {
        $this->assertTrue($this->api->friends->sendRequest('nico'));
    }

    /**
     *
     */
    public function testApproveRequestReturnsExpectedData()
    {
        $this->assertTrue($this->api->friends->approveRequest('nico'));
    }

    /**
     *
     */
    public function testDenyRequestReturnsExpectedData()
    {
        $this->assertTrue($this->api->friends->denyRequest('nico'));
    }

    /**
     *
     */
    public function testUnfriendRequestReturnsExpectedData()
    {
        $this->assertTrue($this->api->friends->unfriend('nico'));
    }
}
