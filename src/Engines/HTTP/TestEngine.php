<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * Handles HTTP requests using native PHP functions. Only requirement: allow_url_fopen
 * @see http://www.php.net/filesystem.configuration#ini.allow-url-fopen
 *
 * If your host doesn't have cURL nor allow_url_fopen, then it's time to change.
 */
namespace PutIO\Engines\HTTP;

use PutIO\Interfaces\HTTP\HTTPEngine;
use PutIO\Helpers\HTTP\HTTPHelper;
use PutIO\Exceptions\RemoteConnectionException;
use PutIO\Exceptions\LocalStorageException;
use PutIO\Exceptions\FileNotFoundException;

final class TestEngine extends HTTPHelper implements HTTPEngine
{
    private $dataDir = 'data';

    /**
     * Makes an HTTP request to put.io's API and returns the response. Relies on native
     * PHP functions.
     *
     * NOTE!! Due to restrictions, files must be loaded into the memory when uploading.
     * I don't recommend uploading large files using native functions. Only use this if
     * you absolutely must! Otherwise, the cURL engine is much better!
     *
     * Downloading is no issue as long as you're saving the file somewhere on the file system
     * rather than the memory. Set $outFile and you're all set!
     *
     * Returns false if a file was not found.
     *
     * @param string $method       HTTP request method. Only POST and GET are supported.
     * @param string $url          Remote path to API module.
     * @param array  $params       OPTIONAL - Variables to be sent.
     * @param string $outFile      OPTIONAL - If $outFile is set, the response will be written to this file instead of StdOut.
     * @param array  $arrayKey     OPTIONAL - Will return all data on a specific array key of the response.
     * @param bool   $verifyPeer   OPTIONAL - If true, will use proper SSL peer/host verification.
     * @return mixed
     * @throws PutIO\Exceptions\PutIOLocalStorageException
     * @throws PutIO\Exceptions\RemoteConnectionException
     */
    public function request($method, $url, array $params = [], $outFile = '', $returnBool = \false, $arrayKey = '', $verifyPeer = \true)
    {
        $url = substr($url, strpos($url, '/v2/') + 4);
        $url = str_replace('/', '_', $url);

        $response = file_get_contents(('tests/data/' . $url . '.json'));
        return $this->getResponse($response, $returnBool, $arrayKey);
        

        return;

        ////////

        if (isset($params['file']) && $params['file'][0] === '@') {
            $file = substr($params['file'], 1);
            unset($params['file']);
            
            if (!$fileData = @file_get_contents($file)) {
                throw new LocalStorageException('Unable to open local file: ' . basename($file));
            }
            
            $data = '';
            $boundary = '---------------------' . substr(md5($file . uniqid('', \true)), 0, 10);
            
            foreach ($params as $key => $value) {
                $data .= "--{$boundary}\n";
                $data .= "Content-Disposition: form-data; name=\"" . $key . "\"\n\n" . $value . "\n";
            }
            
            $data .= "--{$boundary}\n";
            $data .= "Content-Disposition: form-data; name=\"file\"; filename=\"" . basename($file) . '"' . "\n";
            $data .= "Content-Type: " . $this->getMIMEType($file) . "\n";
            $data .= "Content-Transfer-Encoding: binary\n\n";
            $data .= $fileData ."\n";
            $data .= "--{$boundary}--\n";
            
            $contentType = 'multipart/form-data; boundary=' . $boundary;
            $method = 'POST'; // Just in case
            unset($fileData);

            $cnMatch = 'upload.put.io';
        } else {
            $data = http_build_query($params, '', '&');
            $contentType = 'application/x-www-form-urlencoded';
            $cnMatch = 'api.put.io';
        }
        
        $contextOptions = [];
        
        if ($verifyPeer) {
            $cert = realpath(__DIR__ . '/../../Certificates/StarfieldSecureCertificationAuthority.crt');

            $contextOptions['ssl'] = [
                'verify_peer'   => \true,
                'cafile'        => $cert,
                'verify_depth'  => 5,
                'CN_match'      => $cnMatch
            ];
        }
        
        if ($method === 'POST') {
            $contextOptions['http'] = [
                'method' => 'POST',
                'header' =>
                    "Accept: application/json" . "\r\n" .
                    "Content-Type: " . $contentType . "\r\n" .
                    "Content-Length: " . strlen($data) . "\r\n" .
                    "User-Agent: nicoswd-putio/2.0\r\n",
                'content' => $data
            ];
        } else {
            $url .= '?' . $data;
        }

        $context = stream_context_create($contextOptions);
        
        if (($fp = @fopen($url, 'r', \false, $context)) === \false) {
            if (isset($http_response_header) &&
                $this->getResponseCode($http_response_header) === 404) {
                return \false;
            } else {
                throw new RemoteConnectionException(
                    'Unable to connect to remote resource.'
                );
            }
        }
        
        if ($outFile !== '') {
            if (($localfp = @fopen($outFile, 'w+')) === \false) {
                throw new LocalStorageException('Unable to create local file.');
            }
        
            while (!feof($fp)) {
                fputs($localfp, fread($fp, 8192));
            }
            
            fclose($localfp);
            return \true;
        } else {
            $response = '';
            while (!feof($fp)) {
                $response .= fread($fp, 8192);
            }
        }
        
        fclose($fp);
        return $this->getResponse($response, $returnBool, $arrayKey);
    }
}
