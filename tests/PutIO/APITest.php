<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * All HTTP engines must implement this interface.
 */
namespace tests\PutIO;

/**
 * Class APITest
 * @package PutIO\tests
 */
class APITest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    private $api;

    /**
     *
     */
    public function setUp()
    {
        $this->api = new \PutIO\API();
    }

    /**
     *
     */
    public function testSetHTTPEngineWorksWithStringsAndInstances()
    {
        $this->api->setHTTPEngine('Test');
        $this->assertInstanceOf('\PutIO\Interfaces\HTTP\HTTPEngine', $this->api->getHTTPEngine());

        $this->api->setHTTPEngine(new \PutIO\Engines\HTTP\TestEngine);
        $this->assertInstanceOf('\PutIO\Interfaces\HTTP\HTTPEngine', $this->api->getHTTPEngine());
    }

    /**
     *
     */
    public function testMagicSettersSetValues()
    {
        $this->api->setOAuthToken('xxxxx');
        $this->assertSame('xxxxx', $this->api->getOAuthToken());

        $this->api->setSSLVerifyPeer(\false);
        $this->assertSame(\false, $this->api->SSLVerifyPeer);
    }

    /**
     *
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
