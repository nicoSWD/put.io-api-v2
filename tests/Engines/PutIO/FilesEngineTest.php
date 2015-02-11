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
     *
     */
    public function setUp()
    {
        $this->api = new \PutIO\API('test token');
        $this->api->setHTTPEngine('Test');
    }

    /**
     *
     */
    public function testListAllReturnsExpectedData()
    {
        $response = $this->api->files->listall();
        
        $this->assertSame('text/plain', $response[0]['content_type']);
        $this->assertSame('https://put.io/images/file_types/text.png', $response[0]['icon']);
        $this->assertSame(6546533, $response[0]['id']);
        $this->assertSame(\null, $response[0]['opensubtitles_hash']);
    }

    /**
     *
     */
    public function testSearchReturnsExpectedData()
    {
        $response = $this->api->files->search('test');

        $this->assertSame('text/plain', $response['files'][0]['content_type']);
        $this->assertSame('https://put.io/images/file_types/text.png', $response['files'][0]['icon']);
        $this->assertSame('http://api.put.io/v2/files/search/YOUR_QUERY/page/2', $response['next']);
    }

    /**
     *
     */
    public function testUploadReturnsExpectedData()
    {
        $response = $this->api->files->upload(__FILE__);

        $this->assertSame('text/plain', $response['content_type']);
        $this->assertSame('https://put.io/images/file_types/text.png', $response['icon']);
        $this->assertSame(\null, $response['screenshot']);
    }

    /**
     * @expectedException \Exception
     */
    public function testUploadThrowsExceptionIfFileNotFound()
    {
        $this->api->files->upload(md5(microtime(\true)));
    }

    /**
     *
     */
    public function testFileInfoReturnsExpectedData()
    {
        $response = $this->api->files->info(41);

        $this->assertSame('text/plain', $response['content_type']);
        $this->assertSame('https://put.io/images/file_types/text.png', $response['icon']);
        $this->assertSame(\null, $response['screenshot']);
    }

    /**
     *
     */
    public function testDeleteFileReturnsExpectedData()
    {
        $this->assertTrue($this->api->files->delete(41));
        $this->assertTrue($this->api->files->delete([41, 43, 24]));
    }

    /**
     *
     */
    public function testRenameFileReturnsExpectedData()
    {
        $this->assertTrue($this->api->files->rename(41, 'new.txt'));
    }

    /**
     *
     */
    public function testMoveFileReturnsExpectedData()
    {
        $this->assertTrue($this->api->files->move(41, 0));
        $this->assertTrue($this->api->files->move([41, 34, 31], 0));
    }

    /**
     *
     */
    public function testConvertToMP4ReturnsExpectedData()
    {
        $this->assertTrue($this->api->files->convertToMP4(41));
    }

    /**
     *
     */
    public function testMakeDirReturnsExpectedData()
    {
        $info = $this->api->files->makeDir('test', 0);

        $this->assertSame('text/plain', $info['content_type']);
        $this->assertSame(\null, $info['screenshot']);
    }

    /**
     *
     */
    public function testGetMP4StatusReturnsExpectedData()
    {
        $this->assertFalse($this->api->files->getMP4Status(41));
    }

    /**
     *
     */
    public function testDownloadReturnsCorrectValue()
    {
        $this->assertSame(['status' => 'OK'], $this->api->files->download(41, 'test.txt'));
        $this->assertSame(['status' => 'OK'], $this->api->files->download(41));
        $this->assertSame(['status' => 'OK'], $this->api->files->downloadMP4(41));
    }

    /**
     *
     */
    public function testDownloadReturnsFalseForNonExistingFiles()
    {
        $this->assertFalse($this->api->files->download(41311121));
    }

    /**
     *
     */
    public function testGetDownloadURLReturnsCorrectURL()
    {
        $token = $this->api->getOAuthToken();

        $this->assertSame(
            'https://api.put.io/v2/files/41/download?oauth_token=' . $token,
            $this->api->files->getDownloadURL(41)
        );

        $this->assertSame(
            'https://api.put.io/v2/files/41/mp4/download?oauth_token=' . $token,
            $this->api->files->getDownloadURL(41, \true)
        );
    }
}
