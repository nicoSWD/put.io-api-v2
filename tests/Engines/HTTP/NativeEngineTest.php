<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
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
