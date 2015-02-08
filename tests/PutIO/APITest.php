<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 */
namespace tests\PutIO;

/**
 * Class APITest
 * @package tests\PutIO
 */
class APITest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PutIO\API
     */
    private $api;

    /**
     * @covers \PutIO\API::__construct()
     */
    public function setUp()
    {
        $this->api = new \PutIO\API('Test Token');
    }

    /**
     * @covers \PutIO\API::getHTTPEngine()
     */
    public function testGetHTTPEngineReturnsADefaultEngine()
    {
        // If no engine has been specified, this should return a default engine, based on what's installed.
        $this->assertInstanceOf('\PutIO\Interfaces\HTTP\HTTPEngine', $this->api->getHTTPEngine());
    }

    /**
     * @covers \PutIO\API::setHTTPEngine()
     * @covers \PutIO\API::getHTTPEngine()
     */
    public function testSetHTTPEngineWorksWithStringsAndInstances()
    {
        $this->api->setHTTPEngine('Test');
        $this->assertInstanceOf('\PutIO\Engines\HTTP\TestEngine', $this->api->getHTTPEngine());

        $this->api->setHTTPEngine(new \PutIO\Engines\HTTP\TestEngine);
        $this->assertInstanceOf('\PutIO\Engines\HTTP\TestEngine', $this->api->getHTTPEngine());
    }

    /**
     * @covers \PutIO\API::setOAuthToken()
     * @covers \PutIO\API::getOAuthToken()
     * @covers \PutIO\API::setSSLVerifyPeer()
     * @covers \PutIO\API::getSSLVerifyPeer()
     */
    public function testSettersAndGettersSetAndReturnCorrectValues()
    {
        $this->api->setOAuthToken('xxxxx');
        $this->assertSame('xxxxx', $this->api->getOAuthToken());

        $this->api->setSSLVerifyPeer(\false);
        $this->assertSame(\false, $this->api->getSSLVerifyPeer());
    }

    /**
     * @covers \PutIO\API::__get()
     */
    public function testMagicGettersReturnCorrectInstances()
    {
        $this->assertInstanceOf('\PutIO\Engines\PutIO\AccountEngine', $this->api->account);
        $this->assertInstanceOf('\PutIO\Engines\PutIO\FilesEngine', $this->api->files);
        $this->assertInstanceOf('\PutIO\Engines\PutIO\FriendsEngine', $this->api->friends);
        $this->assertInstanceOf('\PutIO\Engines\PutIO\OauthEngine', $this->api->oauth);
        $this->assertInstanceOf('\PutIO\Engines\PutIO\TransfersEngine', $this->api->transfers);
    }
}
