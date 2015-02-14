<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 *
 * Handles HTTP requests using cURL.
 */
namespace PutIO\Engines\HTTP;

use CURLFile;
use PutIO\Exceptions\RemoteConnectionException;
use PutIO\Engines\HTTPEngine;
use PutIO\Helpers\HTTP\HTTPHelper;
use PutIO\Exceptions\LocalStorageException;

/**
 * Class CurlEngine
 * @package PutIO\Engines\HTTP
 */
final class CurlEngine extends HTTPHelper implements HTTPEngine
{
    /**
     * @var bool
     */
    private $verifyPeer = \true;

    /**
     * @return array
     */
    private function getDefaultOptions()
    {
        $options = [
            CURLOPT_USERAGENT      => static::HTTP_USER_AGENT,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => \true,
            CURLOPT_HTTPHEADER     => ['Accept: application/json']
        ];

        if ($this->verifyPeer) {
            $options[CURLOPT_SSL_VERIFYPEER] = \true;
            $options[CURLOPT_SSL_VERIFYHOST] = 2;
            $options[CURLOPT_CAINFO]         = $this->getCertPath();
        } else {
            $options[CURLOPT_SSL_VERIFYPEER] = \false;
            $options[CURLOPT_SSL_VERIFYHOST] = \false;
        }

        return $options;
    }

    /**
     * Makes an HTTP request to put.io's API and returns the response.
     *
     * @param string $method
     * @param string $url
     * @param array $params
     * @param string $outFile
     * @param bool $returnBool
     * @param string $arrayKey
     * @param bool $verifyPeer
     * @return array|bool
     * @throws LocalStorageException
     * @throws RemoteConnectionException
     */
    public function request(
        $method,
        $url,
        array $params = [],
        $outFile = '',
        $returnBool = \false,
        $arrayKey = '',
        $verifyPeer = \true
    ) {
        $this->verifyPeer = $verifyPeer;
        list($url, $options) = $this->configureRequestOptions($url, $method, $params, $outFile);

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $this->handleResponse(curl_errno($ch), curl_error($ch), curl_getinfo($ch, CURLINFO_HTTP_CODE));

        return $this->getResponse($response, $returnBool, $arrayKey);
    }

    /**
     * @param int    $errNum
     * @param string $error
     * @param int    $responseCode
     * @throws RemoteConnectionException
     */
    private function handleResponse($errNum, $error, $responseCode)
    {
        if ($errNum) {
            throw new RemoteConnectionException($error, $errNum);
        }

        if ($responseCode !== 200) {
            throw new RemoteConnectionException(
                "Unexpected Response: {$responseCode}",
                $responseCode
            );
        }
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $params
     * @param string $outFile
     * @return array
     * @throws LocalStorageException
     */
    private function configureRequestOptions($url, $method, array $params, $outFile)
    {
        $options = $this->getDefaultOptions();

        if ($method === 'POST') {
            $options += $this->post($params);

            if (isset($params['file']) && $params['file'][0] === '@') {
                $url  = static::API_UPLOAD_URL . $url;
                $url .= '?oauth_token=' . $params['oauth_token'];
            } else {
                $url = static::API_URL . $url;
            }
        } else {
            $url  = static::API_URL . $url . '?';
            $url .= http_build_query($params, '', '&');
        }

        if ($outFile === '') {
            $options[CURLOPT_RETURNTRANSFER] = \true;
        } else {
            if (($fp = @fopen($outFile, 'w+')) === \false) {
                throw new LocalStorageException('Unable to create local file');
            }

            $options[CURLOPT_FILE] = $fp;
        }

        return [$url, $options];
    }

    /**
     * @param array $params
     * @return array
     */
    private function post(array $params)
    {
        $options = [
            CURLOPT_POST => \true
        ];

        if (isset($params['file'])) {
            // @php >= 5.5
            if (class_exists('\CURLFile')) {
                $filePath = realpath(substr($params['file'], 1));

                $params['file'] = new CURLFile(
                    $filePath,
                    $this->getMIMEType($filePath),
                    basename($filePath)
                );

                $options[CURLOPT_SAFE_UPLOAD] = \true;
            }
        }

        $options[CURLOPT_POSTFIELDS] = $params;
        return $options;
    }
}
