<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * Handles HTTP requests using native functions.
 *
 * NOTE: File uploads are NOT supported yet using native functions.
 * If you really must upload files, then use the cURL engine.
 *
**/

namespace PutIO\Engines\HTTP;

use PutIO\ClassEngine;
use PutIO\Interfaces\HTTPEngine;
use PutIO\Exceptions\RemoteConnectionException;
use PutIO\Exceptions\LocalStorageException;


class Native implements HTTPEngine
{
    
    /**
     * Makes an HTTP request to put.io's API and returns the response.
     *
     * @param string $method    HTTP request method. Only POST and GET are supported.
     * @param string $url       Remote path to API module.
     * @param array  $params    OPTIONAL - Variables to be sent.
     * @param string $outFile   OPTIONAL - If $outFile is set, the response will be written to this file instead of StdOut.
     * @return mixed
     * @throws PutIOLocalStorageException
     * @throws RemoteConnectionException
     *
    **/
    public function request($method, $url, array $params = array(), $outFile = '', $returnBool = false)
    {
        $params = http_build_query($params, '', '&');
        
        if ($method === 'POST')
        {
            $contextOptions = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n"
                        . "Content-Length: " . strlen($params) . "\r\n"
                        . "User-Agent: nicoswd-putio/2.0\r\n",
                    'content' => $params
                )
            );
        }
        else
        {
            $contextOptions = array();
            $url .= '?' . $params;
        }

        $context = stream_context_create($contextOptions);
        
        if (($fp = @fopen($url, 'r', false, $context)) === false)
        {
            throw new RemoteConnectionException('Unable to connect to remote resource.');
        }
        
        if ($outFile !== '')
        {
            if (($localfp = @fopen($outFile, 'w+')) === false)
            {
                throw new LocalStorageException('Unable to create local file.');
            }
        
            while (!feof($fp))
            {
                fputs($localfp, fread($fp, 8192));
            }
            
            fclose($fp);
            fclose($localfp);
            
            return true;
        }
        else
        {
            $response = '';
            
            while (!feof($fp))
            {
                $response .= fread($fp, 8192);
            }
        }
        
        if (($response = json_decode($response, true)) === null)
        {
            return false;
        }
        
        if ($returnBool)
        {
            if (isset($response['status']) AND $response['status'] === 'OK')
            {
                return true;
            }
            
            return false;
        }
        
        return $response;
    }
}


?>