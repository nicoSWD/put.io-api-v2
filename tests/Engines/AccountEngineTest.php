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
class AccountEngineTest extends \PHPUnit_Framework_TestCase
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
     * @covers \PutIO\Engines\PutIO\AccountEngine::info()
     * @covers \PutIO\Engines\HTTP\TestEngine::request()
     * @covers \PutIO\Helpers\HTTP\HTTPHelper::jsonDecode()
     * @covers \PutIO\Helpers\HTTP\HTTPHelper::getResponse()
     */
    public function testAccountEngineReturnsCorrectInfoData()
    {
        $response = $this->api->account->info();
        
        $this->assertSame('cenk', $response['username']);
        $this->assertSame('cenk@gmail.com', $response['mail']);
        $this->assertSame('2014-03-04T06:33:30', $response['plan_expiration_date']);
        $this->assertSame(['tr', 'eng'], $response['subtitle_languages']);
        $this->assertSame('tr', $response['default_subtitle_language']);
        $this->assertSame([
            'avail' => 20849243836,
            'used'  => 32837847364,
            'size'  => 53687091200
        ], $response['disk']);
    }

    /**
     * @covers \PutIO\Engines\PutIO\AccountEngine::settings()
     */
    public function testAccountEngineReturnsCorrectSettingsData()
    {
        $response = $this->api->account->settings();

        $this->assertSame(0, $response['default_download_folder']);
        $this->assertSame(\false, $response['is_invisible']);
        $this->assertSame(\false, $response['extraction_default']);
        $this->assertSame(['tr', 'eng'], $response['subtitle_languages']);
        $this->assertSame('tr', $response['default_subtitle_language']);
    }
}
