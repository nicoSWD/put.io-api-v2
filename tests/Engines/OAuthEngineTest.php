<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 */
namespace tests\Engines;

/**
 * Class AccountEngineTest
 * @package tests\Engines
 */
class OAuthEngineTest extends \PHPUnit_Framework_TestCase
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
    public function testVerifyAccessToken()
    {
        $clientID = 123;
        $clientSecret = 456;
        $redirectURI = 'https://example.org/callback';
        $code = '123';

        $token = $this->api->oauth->verifyCode($clientID, $clientSecret, $redirectURI, $code);
        
        $this->assertSame('abc123', $token);
    }
}
