<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
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
     * @covers \PutIO\Engines\PutIO\FriendsEngine::listall()
     */
    public function testListAllReturnsExpectedData()
    {
        $response = $this->api->friends->listall();
        
        $this->assertSame('foo', $response[0]['name']);
        $this->assertSame('bar', $response[1]['name']);
    }

    /**
     * @covers \PutIO\Engines\PutIO\FriendsEngine::pendingRequests()
     */
    public function testPendingRequestsReturnsExpectedData()
    {
        $response = $this->api->friends->pendingRequests();
        
        $this->assertSame('foo', $response[0]['name']);
        $this->assertSame('bar', $response[1]['name']);
    }

    /**
     * @covers \PutIO\Engines\PutIO\FriendsEngine::sendRequest()
     */
    public function testSendRequestReturnsExpectedData()
    {
        $this->assertTrue($this->api->friends->sendRequest('nico'));
    }

    /**
     * @covers \PutIO\Engines\PutIO\FriendsEngine::approveRequest()
     */
    public function testApproveRequestReturnsExpectedData()
    {
        $this->assertTrue($this->api->friends->approveRequest('nico'));
    }

    /**
     * @covers \PutIO\Engines\PutIO\FriendsEngine::denyRequest()
     */
    public function testDenyRequestReturnsExpectedData()
    {
        $this->assertTrue($this->api->friends->denyRequest('nico'));
    }

    /**
     * @covers \PutIO\Engines\PutIO\FriendsEngine::unfriend()
     */
    public function testUnfriendRequestReturnsExpectedData()
    {
        $this->assertTrue($this->api->friends->unfriend('nico'));
    }
}
