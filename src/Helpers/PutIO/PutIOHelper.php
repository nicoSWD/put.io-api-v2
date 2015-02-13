<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * This class helps PutIO engines to do common tasks.
 */
namespace PutIO\Helpers\PutIO;

use PutIO\API;

/**
 * Class PutIOHelper
 * @package PutIO\Helpers\PutIO
 */
class PutIOHelper
{
    /**
     * Holds the main PutIO class instance.
     *
     * @var API|null
     */
    protected $putio = \null;
    
    /**
     * Holds the instance of the HTTP Engine class
     *
     * @var \PutIO\Engines\HTTPEngine|null
     */
    protected $HTTPEngine = \null;

    /**
     * Class constructor. Stores an instance of PutIO.
     *
     * @param API $putio    Instance of PutIO\API
     */
    public function __construct(API $putio)
    {
        $this->putio = $putio;
    }
    
    /**
     * Sends a GET HTTP request.
     *
     * @param string $path         Path of the API class.
     * @param array  $params       GET variables to be sent.
     * @param bool   $returnBool   Will return boolean if true.
     * @param string $arrayKey     Will return all data on a specific array key
     *                                  of the response.
     * @return mixed
     */
    protected function get(
        $path,
        array $params = [],
        $returnBool = \false,
        $arrayKey = ''
    ) {
        return $this->request('GET', $path, $params, '', $returnBool, $arrayKey);
    }

    /**
     * Sends a POST HTTP request.
     *
     * @param string $path         Path of the API class.
     * @param array  $params       POST variables to be sent.
     * @param bool   $returnBool   Will return boolean if true. 
     * @param string $arrayKey     Will return all data on a specific array key
     *                                  of the response.
     * @return mixed
     */
    protected function post(
        $path,
        array $params = [],
        $returnBool = \false,
        $arrayKey = ''
    ) {
        return $this->request('POST', $path, $params, '', $returnBool, $arrayKey);
    }
    
    /**
     * Downloads a remote file to the local server.
     *
     * TIP: The download can take a while, and it's possible that
     * the script time outs. To prevent that, call set_time_limit(0);
     * before attempting to download the file.
     *
     * @param string $path    Path to remote file.
     * @param string $saveAS  Path to local file.
     * @return bool
     */
    protected function downloadFile($path, $saveAS)
    {
        return $this->request('GET', $path, [], $saveAS, \true);
    }
    
    /**
     * Uploads a local file to the remote server.
     * 
     * TIP: The upload can take a while, and it's possible that
     * the script time outs. To prevent that, call set_time_limit(0);
     * before attempting to upload the file.
     *
     * @param string $path     Path to file you want to upload.
     * @param array  $params   Addition variables to be sent.
     * @return bool
     */
    protected function uploadFile($path, array $params = [])
    {
        return $this->request('POST', $path, $params, '', \false, 'file');
    }
    
    /**
     * Makes an HTTP request to put.io's API and returns the response.
     *
     * @param string $method    HTTP request method. Only POST and GET.
     * @param string $path      Remote path to API module.
     * @param array  $params    Variables to be sent.
     * @param string $outFile   If $outFile is set, the response will be written
     *                              to this file instead of StdOut.
     * @param bool   $returnBool
     * @param string $arrayKey  Will return all data on a specific array key of
     *                              the response.
     * @return mixed
     * @throws \PutIO\Exceptions\LocalStorageException
     */
    protected function request(
        $method,
        $path,
        array $params = [],
        $outFile = '',
        $returnBool = \false,
        $arrayKey = ''
    ) {
        if ($token = $this->putio->getOAuthToken()) {
            $params['oauth_token'] = $token;
        }

        $engine = $this->putio->getHTTPEngine();
        $verifyPeer = $this->putio->getSSLVerifyPeer();
        
        return $engine->request(
            $method,
            $path,
            $params,
            $outFile,
            $returnBool,
            $arrayKey,
            $verifyPeer
        );
    }
}
