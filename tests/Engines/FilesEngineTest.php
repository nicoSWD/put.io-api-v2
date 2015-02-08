<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 */
namespace tests\Engines;

/**
 * Class FilesEngineTest
 * @package tests\Engines
 */
class FilesEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PutIO\API
     */
    private $api;

    /**
     * @covers \PutIO\Helpers\PutIO\PutIOHelper::__construct()
     */
    public function setUp()
    {
        $this->api = new \PutIO\API('test token');
        $this->api->setHTTPEngine('Test');
    }

    /**
     * @covers \PutIO\Engines\PutIO\FilesEngine::listall()
     * @covers \PutIO\Helpers\PutIO\PutIOHelper::get()
     */
    public function testListAllReturnsExpectedData()
    {
        $response = $this->api->files->listall();
        
        $this->assertSame('text/plain', $response[0]['content_type']);
        $this->assertSame('https://put.io/images/file_types/text.png', $response[0]['icon']);
        $this->assertSame(6546533, $response[0]['id']);
        $this->assertSame(null, $response[0]['opensubtitles_hash']);
    }

    /**
     * @covers \PutIO\Engines\PutIO\FilesEngine::search()
     * @covers \PutIO\Helpers\PutIO\PutIOHelper::request()
     */
    public function testSearchReturnsExpectedData()
    {
        $response = $this->api->files->search('test');

        $this->assertSame('text/plain', $response['files'][0]['content_type']);
        $this->assertSame('https://put.io/images/file_types/text.png', $response['files'][0]['icon']);
        $this->assertSame('http://api.put.io/v2/files/search/YOUR_QUERY/page/2', $response['next']);
    }

    /**
     * @covers \PutIO\Engines\PutIO\FilesEngine::upload()
     * @covers \PutIO\Helpers\HTTP\HTTPHelper::getMIMEType()
     */
    public function testUploadReturnsExpectedData()
    {
        $response = $this->api->files->upload('test.txt');

        $this->assertSame('text/plain', $response['content_type']);
        $this->assertSame('https://put.io/images/file_types/text.png', $response['icon']);
        $this->assertSame(\null, $response['screenshot']);
    }

    /**
     * @covers \PutIO\Engines\PutIO\FilesEngine::info()
     * @covers \PutIO\Helpers\PutIO\PutIOHelper::get()
     */
    public function testFileInfoReturnsExpectedData()
    {
        $response = $this->api->files->info(41);

        $this->assertSame('text/plain', $response['content_type']);
        $this->assertSame('https://put.io/images/file_types/text.png', $response['icon']);
        $this->assertSame(\null, $response['screenshot']);
    }

    /**
     * @covers \PutIO\Engines\PutIO\FilesEngine::delete()
     * @covers \PutIO\Helpers\PutIO\PutIOHelper::post()
     */
    public function testDeleteFileReturnsExpectedData()
    {
        $this->assertTrue($this->api->files->delete(41));
    }

    /**
     * @covers \PutIO\Engines\PutIO\FilesEngine::rename()
     * @covers \PutIO\Helpers\PutIO\PutIOHelper::post()
     */
    public function testRenameFileReturnsExpectedData()
    {
        $this->assertTrue($this->api->files->rename(41, 'new.txt'));
    }

    /**
     * @covers \PutIO\Engines\PutIO\FilesEngine::move()
     * @covers \PutIO\Helpers\PutIO\PutIOHelper::post()
     */
    public function testMoveFileReturnsExpectedData()
    {
        $this->assertTrue($this->api->files->move(41, 0));
    }

    /**
     * @covers \PutIO\Engines\PutIO\FilesEngine::convertToMP4()
     * @covers \PutIO\Helpers\PutIO\PutIOHelper::post()
     */
    public function testConvertToMP4ReturnsExpectedData()
    {
        $this->assertTrue($this->api->files->convertToMP4(41));
    }

    /**
     * @covers \PutIO\Engines\PutIO\FilesEngine::getDownloadURL()
     */
    public function testGetDownloadURLReturnsCorrectURL()
    {
        $this->assertSame('https://api.put.io/v2/files/41/download', $this->api->files->getDownloadURL(41));
        $this->assertSame('https://api.put.io/v2/files/41/mp4/download', $this->api->files->getDownloadURL(41, \true));
    }
}
