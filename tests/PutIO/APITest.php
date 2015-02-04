<?php

class APITest extends \PHPUnit_Framework_TestCase
{
    private $api;

    public function setUp()
    {
        $this->api = new PutIO\API();
    }

    public function testMagicSettersSetValues()
    {
        $this->api->setOAuthToken('xxxxx');
        $this->assertSame('xxxxx', $this->api->OAuthToken);

        $this->api->setHTTPEngine('Test');
        $this->assertSame('Test', $this->api->HTTPEngine);

        $this->api->setSSLVerifyPeer(false);
        $this->assertSame(false, $this->api->SSLVerifyPeer);
    }

    /**
     * @expectedException PutIO\Exceptions\UndefinedMethodException
     */
    public function testMagicSettersThrowsExceptionForInvalidMethods()
    {
        $this->api->setWhatEver(true);
    }

    /**
     * @expectedException PutIO\Exceptions\MissingParamException
     */
    public function testMagicSettersThrowsExceptionForMissingParams()
    {
        $this->api->setSSLVerifyPeer();
    }

    public function testMagicGettersReturnCorrectInstances()
    {
        $this->assertInstanceOf('\PutIO\Engines\PutIO\AccountEngine', $this->api->account);
        $this->assertInstanceOf('\PutIO\Engines\PutIO\FilesEngine', $this->api->files);
        $this->assertInstanceOf('\PutIO\Engines\PutIO\FriendsEngine', $this->api->friends);
        $this->assertInstanceOf('\PutIO\Engines\PutIO\OauthEngine', $this->api->oauth);
        $this->assertInstanceOf('\PutIO\Engines\PutIO\TransfersEngine', $this->api->transfers);
    }
}
