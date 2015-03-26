<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 *
 * Handles HTTP requests using native PHP functions.
 * Only requirement: allow_url_fopen
 * @see http://www.php.net/filesystem.configuration#ini.allow-url-fopen
 *
 * If your host doesn't have cURL nor allow_url_fopen, then it's time to change.
 */
declare(strict_types=1);

namespace PutIO\Engines\HTTP;

use PutIO\Engines\HTTPEngine;
use PutIO\Helpers\HTTP\HTTPHelper;
use PutIO\Exceptions\RemoteConnectionException;
use PutIO\Exceptions\LocalStorageException;

/**
 * Class NativeEngine
 * @package PutIO\Engines\HTTP
 */
final class NativeEngine extends HTTPHelper implements HTTPEngine
{
    /**
     * Makes an HTTP request to put.io's API and returns the response.
     * Relies on native PHP functions.
     *
     * NOTE!! Due to restrictions, files must be loaded into the memory when
     * uploading. I don't recommend uploading large files using native
     * functions. Only use this if you absolutely must! Otherwise, the cURL
     * engine is much better!
     *
     * Downloading is no issue as long as you're saving the file somewhere on
     * the file system rather than the memory. Set $outFile and you're all set!
     *
     * Returns false if a file was not found.
     *
     * @param string $method      HTTP request method. Only POST and GET are
     *                                  supported.
     * @param string $url         Remote path to API module.
     * @param array  $params      Variables to be sent.
     * @param string $outFile     If $outFile is set, the response will be
     *                                  written to this file instead of StdOut.
     * @param bool   $returnBool
     * @param string $arrayKey    Will return all data on a specific array key
     *                                  of the response.
     * @param bool   $verifyPeer  If true, will use proper SSL peer/host
     *                                  verification.
     * @return mixed
     * @throws \PutIO\Exceptions\LocalStorageException
     * @throws \PutIO\Exceptions\RemoteConnectionException
     */
    public function request(
        string $method,
        string $url,
        array $params = [],
        string $outFile = '',
        bool $returnBool = \false,
        string $arrayKey = '',
        bool $verifyPeer = \true
    ) : bool {
        list($url, $contextOptions) = $this->configureRequestOptions($url, $method, $params, $verifyPeer);
        $fp = @fopen($url, 'rb', \false, stream_context_create($contextOptions));
        $headers = stream_get_meta_data($fp)['wrapper_data'];

        return $this->handleRequest($fp, $headers, $outFile, $returnBool, $arrayKey);
    }

    /**
     * @param resource $fp
     * @param array    $responseHeaders
     * @param string   $outFile
     * @param bool     $returnBool
     * @param string   $arrayKey
     * @return array|bool|int
     * @throws LocalStorageException
     * @throws RemoteConnectionException
     */
    private function handleRequest($fp, array $responseHeaders, string $outFile, bool $returnBool, string $arrayKey)
    {
        if ($fp === \false) {
            throw new RemoteConnectionException(
                "Unable to connect to host"
            );
        }

        $responseCode = $this->getResponseCode($responseHeaders);

        if ($responseCode !== 200) {
            throw new RemoteConnectionException(
                "Unexpected Response: {$responseCode}",
                $responseCode
            );
        }

        if ($outFile !== '') {
            return $this->writeToFile($fp, $outFile);
        }

        return $this->getResponse(
            $this->readResponse($fp),
            $returnBool,
            $arrayKey
        );
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $params
     * @param bool   $verifyPeer
     * @return array
     * @throws LocalStorageException
     */
    private function configureRequestOptions(string $url, string $method, array $params, bool $verifyPeer) : array
    {
        if (isset($params['file']) && $params['file'][0] === '@') {
            $boundary  = '---------------------';
            $boundary .= substr(md5(uniqid('', \true)), 0, 10);

            $url  = static::API_UPLOAD_URL . $url;
            $url .= '?oauth_token=' . $params['oauth_token'];

            $data = $this->getPostData($params, $boundary);
            $contentType = 'multipart/form-data; boundary=' . $boundary;
            $cnMatch = 'upload.put.io';
        } else {
            $data = http_build_query($params, '', '&');
            $contentType = 'application/x-www-form-urlencoded';
            $cnMatch = 'api.put.io';
            $url = static::API_URL . $url;
        }

        $contextOptions = [];
        $contextOptions['http']['header'] =
            "User-Agent: " . static::HTTP_USER_AGENT . "\r\n" .
            "Accept: application/json" . "\r\n" .
            "Content-Type: " . $contentType . "\r\n";

        if ($verifyPeer) {
            $contextOptions['ssl'] = [
                'verify_peer'       => \true,
                'allow_self_signed' => \false,
                'cafile'            => $this->getCertPath(),
                'verify_depth'      => 5,
                'peer_name'         => $cnMatch
            ];
        }

        $contextOptions['http']['method'] = $method;

        if ($method === 'POST') {
            $contextOptions['http']['content'] = $data;
            $contextOptions['http']['header'] .= "Content-Length: " . strlen($data) . "\r\n";
        } else {
            $url .= '?' . $data;
        }

        return [$url, $contextOptions];
    }

    /**
     * @param resource $fp
     * @param string   $outFile
     * @return int             Number of bytes written
     * @throws LocalStorageException
     */
    private function writeToFile($fp, string $outFile) : int
    {
        if (($localFp = @fopen($outFile, 'w+')) === \false) {
            throw new LocalStorageException(
                'Unable to create local file. Check permissions.'
            );
        }

        $written = stream_copy_to_stream($fp, $localFp);
        
        fclose($localFp);
        fclose($fp);

        return $written;
    }

    /**
     * @param resource $fp
     * @return string
     */
    private function readResponse($fp) : string
    {
        $response = '';

        while (!feof($fp)) {
            $response .= fread($fp, 8192);
        }

        fclose($fp);
        return $response;
    }

    /**
     * @param array  $params
     * @param string $boundary
     * @return string
     * @throws LocalStorageException
     */
    private function getPostData(array $params, string $boundary) : string
    {
        $data = '';
        $filePath = substr($params['file'], 1);
        unset($params['file']);

        if (($fileData = @file_get_contents($filePath)) === \false) {
            throw new LocalStorageException(
                "Unable to open local file: {$filePath}"
            );
        }

        foreach ($params as $key => $value) {
            $data .= "--{$boundary}\n";
            $data .= "Content-Disposition: form-data; ";
            $data .= "name=\"" . $key . "\"\n\n" . $value . "\n";
        }

        $data .= "--{$boundary}\n";
        $data .= "Content-Disposition: form-data; name=\"file\"; filename=\"";
        $data .= basename($filePath) . '"' . "\n";
        $data .= "Content-Type: " . $this->getMIMEType($filePath) . "\n";
        $data .= "Content-Transfer-Encoding: binary\n\n";
        $data .= $fileData ."\n";
        $data .= "--{$boundary}--\n";

        return $data;
    }
}
