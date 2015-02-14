<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 */
namespace tests\Engines;

/**
 * Class CurlEngineTest
 * @package tests\Engines
 */
class NativeEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PutIO\Engines\HTTP\CurlEngine
     */
    private $engine;

    /**
     *
     */
    public function setUp()
    {
        $this->engine = new \PutIO\Engines\HTTP\NativeEngine();
    }

    /**
     *
     */
    public function testReadResponseReturnsReadData()
    {
        if (($fp = tmpfile()) !== \false) {
            fwrite($fp, 'test');
            fseek($fp, 0);

            $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'readResponse');
            $method->setAccessible(\true);

            // NativeEngine::readResponse closes the resource
            $response = $method->invoke($this->engine, $fp);

            $this->assertSame('test', $response);
        } else {
            $this->markTestIncomplete(
                'Test has been skipped because it requires writing permissions'
            );
        }
    }

    /**
     * @expectedException \PutIO\Exceptions\RemoteConnectionException
     */
    public function testHandleRequestThrowsExceptionIfFopenFailed()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'handleRequest');
        $method->setAccessible(\true);

        $method->invokeArgs($this->engine, [\false, [], '', '', '']);
    }

    /**
     * @expectedException \PutIO\Exceptions\RemoteConnectionException
     */
    public function testHandleRequestThrowsExceptionOnNon200StatusCodes()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'handleRequest');
        $method->setAccessible(\true);

        if (($fp = tmpfile()) !== \false) {
            $method->invokeArgs($this->engine, [$fp, [0 => 'HTTP/1.1 401 Unauthorized'], '', '', '']);
        } else {
            $this->markTestIncomplete('File writing permissions required');
        }
    }

    /**
     *
     */
    public function testHandleRequestWritesToOutfileIfSpecified()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'handleRequest');
        $method->setAccessible(\true);

        if (($fp = tmpfile()) !== \false) {
            fwrite($fp, 'test');
            fseek($fp, 0);
            $outFile = tempnam(sys_get_temp_dir(), time());

            $response = $method->invokeArgs($this->engine, [$fp, [0 => 'HTTP/1.1 200 OK'], $outFile, '', '']);

            $this->assertSame(4, $response);
            $this->assertSame('test', trim(file_get_contents($outFile)));
        } else {
            $this->markTestIncomplete('File writing permissions required');
        }
    }

    /**
     *
     */
    public function testHandleRequestWritesReturnsDecodedJSON()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'handleRequest');
        $method->setAccessible(\true);

        if (($fp = tmpfile()) !== \false) {
            fwrite($fp, '{"status": "ok"}');
            fseek($fp, 0);

            $response = $method->invokeArgs($this->engine, [$fp, [0 => 'HTTP/1.1 200 OK'], '', '', '']);
            $this->assertSame(['status' => 'ok'], $response);
        } else {
            $this->markTestIncomplete('File writing permissions required');
        }
    }

    /**
     *
     */
    public function testConfigureRequestOptionsReturnsExpectedArray()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'configureRequestOptions');
        $method->setAccessible(\true);

        $response = $method->invokeArgs($this->engine, ['account/info', 'POST', [], \true]);

        $this->assertSame('https://api.put.io/v2/account/info', $response[0]);
        $this->assertSame('POST', $response[1]['http']['method']);
        $this->assertSame('', $response[1]['http']['content']);

        $this->assertTrue($response[1]['ssl']['verify_peer']);
        $this->assertSame('api.put.io', $response[1]['ssl']['CN_match']);
        $this->assertSame('api.put.io', $response[1]['ssl']['peer_name']);

        // Without verify peer, no SSL options should be generated.
        $response = $method->invokeArgs($this->engine, ['account/info', 'POST', [], \false]);
        $this->assertArrayNotHasKey('ssl', $response[1]);

        $tmpName = tempnam(sys_get_temp_dir(), time());

        if ($fp = @fopen($tmpName, 'w+')) {
            fwrite($fp, 'test');
            fclose($fp);

            $response = $method->invokeArgs($this->engine, ['account/info', 'POST', ['file' => '@' . $tmpName, 'oauth_token'=> '123'], \true]);

            $this->assertSame('upload.put.io', $response[1]['ssl']['CN_match']);
            $this->assertSame('upload.put.io', $response[1]['ssl']['peer_name']);
        } else {
            $this->markTestIncomplete(
                'Test has been skipped because it requires writing permissions'
            );
        }

        $response = $method->invokeArgs($this->engine, ['account/info', 'GET', [], \true]);
        $this->assertSame('GET', $response[1]['http']['method']);
    }

    /**
     *
     */
    public function testGetPostDataReturnsSatisfactoryData()
    {
        $tmpName = tempnam(sys_get_temp_dir(), time());

        if (($fp = fopen($tmpName, 'w')) !== \false) {
            fwrite($fp, 'test');
            fclose($fp);

            $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'getPostData');
            $method->setAccessible(\true);

            $params = [
                'foo' => 'bar',
                'bar' => 'foo',
                'file' => '@' . $tmpName
            ];

            $boundary = '---test---';
            $response = trim($method->invokeArgs($this->engine, [$params, $boundary]));

            $expected = '-----test---
Content-Disposition: form-data; name="foo"

bar
-----test---
Content-Disposition: form-data; name="bar"

foo
-----test---
Content-Disposition: form-data; name="file"; filename="' . basename($tmpName) . '"
Content-Type: text/plain
Content-Transfer-Encoding: binary

test
-----test-----';

            $this->assertEquals($expected, $response);
        } else {
            $this->markTestIncomplete(
                'Test has been skipped because it requires writing permissions'
            );
        }
    }

    /**
     * @expectedException \PutIO\Exceptions\LocalStorageException
     */
    public function testGetPostDataThrowsExceptionIfFileNotFound()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'getPostData');
        $method->setAccessible(\true);

        $params = [
            'foo' => 'bar',
            'bar' => 'foo',
            'file' => '@fileSomethingSomeThing'
        ];

        $method->invokeArgs($this->engine, [$params, '']);
    }

    /**
     *
     */
    public function testWriteToFileWritesString()
    {
        $tmpName = tempnam(sys_get_temp_dir(), time());

        if (($fp = fopen($tmpName, 'w+')) !== \false) {
            fwrite($fp, 'test');
            fseek($fp, 0);

            $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'writeToFile');
            $method->setAccessible(\true);

            $outFile = tempnam(sys_get_temp_dir(), time());

            $bytesWritten = $method->invokeArgs($this->engine, [$fp, $outFile]);
            $this->assertSame(4, $bytesWritten);
        } else {
            $this->markTestIncomplete(
                'Test has been skipped because it requires writing permissions'
            );
        }
    }

    /**
     * @expectedException \PutIO\Exceptions\LocalStorageException
     */
    public function testWriteToFileThrowsExceptionIfFileNotFound()
    {
        $method = new \ReflectionMethod('\PutIO\Engines\HTTP\NativeEngine', 'writeToFile');
        $method->setAccessible(\true);

        $method->invokeArgs($this->engine, [null, '/\/\/\/']);
    }
}
