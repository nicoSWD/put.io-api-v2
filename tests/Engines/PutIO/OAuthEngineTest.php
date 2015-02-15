<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
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

    /**
     *
     */
    public function testRequestPermissionSendsCorrectHeader()
    {
        $clientID = 123;
        $redirectURI = 'http://localhost';

        $method = new \ReflectionMethod('\PutIO\Engines\PutIO\OauthEngine', 'getRedirectURL');
        $method->setAccessible(\true);

        $url = $method->invokeArgs($this->api->oauth, [$clientID, $redirectURI]);

        $expected = 'https://api.put.io/v2/oauth2/authenticate?client_id=123&response_type=';
        $expected .= 'code&redirect_uri=http%3A%2F%2Flocalhost';

        $this->assertSame($expected, $url);
    }
}
