<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * This class handles all HTTP requests.
 *
**/

abstract class ClassEngine
{
    
    /**
     * Holds the main PutIO class instance.
     *
    **/
    protected $putio        = null;
    
    /**
     * The main URL to the API (v2).
     *
    **/
    const API_URL           = 'https://api.put.io/v2/';
    
    /**
     * Class version.
     *
    **/
    const APP_VERSION       = '2.0';
    
    
    /**
     * Class constructor. Stores an instance of PutIO.
     *
     * @param PutIO $putio    Instance of PutIO
     * @return void
     *
    **/
    public function __construct(PutIO $putio)
    {
        $this->putio = $putio;
    }
    
    
    /**
     * Sends a GET HTTP request.
     *
     * @param string  $path         Path of the API class.
     * @param array   $params       OPTIONAL - GET variables to be sent.
     * @param boolean $returnBool   OPTIONAL - Will return boolean if true. True if $response['status'] === 'OK'.
     * @return mixed
     *
    **/
    protected function get($path, array $params = array(), $returnBool = false)
    {
        return $this->request('GET', $path, $params, '', $returnBool);
    }
    

    /**
     * Sends a POST HTTP request.
     *
     * @param string  $path         Path of the API class.
     * @param array   $params       OPTIONAL - POST variables to be sent.
     * @param boolean $returnBool   OPTIONAL - Will return boolean if true. True if $response['status'] === 'OK'.
     * @return mixed
     *
    **/   
    protected function post($path, array $params = array(), $returnBool = false)
    {
        return $this->request('POST', $path, $params, '', $returnBool);
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
     * @return boolean
     *
    **/
    protected function downloadFile($path, $saveAS)
    {
        return $this->request('GET', $path, array(), $saveAS);
    }
    
    
    /**
     * Uploads a local file to the remote server.
     * 
     * TIP: The upload can take a while, and it's possible that
     * the script time outs. To prevent that, call set_time_limit(0);
     * before attempting to upload the file.
     *
     * @param string $path     Path to file you want to upload.
     * @param array  $params   OPTIONAL - Addition variables to be sent.
     * @return boolean
     * 
    **/
    protected function uploadFile($path, array $params = array())
    {
        return $this->request('POST', $path, $params);
    }
    
    
    /**
     * Makes an HTTP request to put.io's API and returns the response.
     *
     * @param string $method    HTTP request method. Only POST and GET are supported.
     * @param string $path      Remote path to API module.
     * @param array  $params    OPTIONAL - Variables to be sent.
     * @param string $outFile   OPTIONAL - If $outFile is set, the response will be written to this file instead of StdOut.
     * @return mixed
     * @throws \Exception
     *
    **/
    protected function request($method, $path, array $params = array(), $outFile = '', $returnBool = false)
    {
        if (!$params['oauth_token'] = $this->putio->oauthToken)
        {
            throw new \Exception('Missing oauth_token');
        }
        
        $url = static::API_URL . $path;
        $ch = curl_init();
            
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'nicoswd-putio/' . static::APP_VERSION
        ));
        
        if ($method === 'POST')
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        else if ($method === 'GET')
        {
            $url .= '?' . http_build_query($params, '', '&');
            curl_setopt($ch, CURLOPT_POST, false);
        }
        else
        {
            throw new \Exception('Method not supported');
        }
        
        if ($outFile !== '')
        {
            if (($outFile = @fopen($outFile, 'w+')) === false)
            {
                throw new \Exception('Unable to create local file');
            }

            curl_setopt($ch, CURLOPT_FILE, $outFile);
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        
        // if (curl_getinfo($ch))
        
        if (($response = json_decode($response, true)) === null)
        {
            return false;
        }
        
        if ($returnBool)
        {
            return ((isset($response['status']) AND $response['status'] === 'OK'));
        }
        
        return $response;
    }
}

?>