<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * Handles HTTP requests using cURL.
 *
**/

namespace PutIO\Engines\HTTP\Helpers;


class HTTPHelper
{
    
    /**
     * Returns true if the server responded with status === OK
     *
     * @param array $response    Response from remote server.
     * @return boolean
     *
    **/
    public static function getStatus(array $response)
    {
        if (isset($response['status']) AND $response['status'] === 'OK')
        {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Parses the response header from the server, fetches the
     * HTTP status code, and returns it.
     *
     * @param array $headers    Array containing response headers
     * @return integer
     *
    **/
    public static function getResponseCode(array $headers)
    {
        if (preg_match('~HTTP/1.1\s+(\d+)~', $headers[0], $match))
        {
            return (int) $match[1];
        }
        
        return 0;
    }
    
    
    /**
     * Attemps to get the MIME type of a given file.
     * 
     * Relies on the file info extension, which is shipped with PHP 5.3
     * and enabled by default. So,... nothing should go wrong, RIGHT?
     *
     * @param string $file    Path of the file you want to get the MIME type of.
     * @return string
     *
    **/
    public static function getMIMEType($file)
    {
        if (function_exists('finfo_open') AND $info = @finfo_open(FILEINFO_MIME))
        {
            if (($mime = @finfo_file($info, $file)) !== false)
            {
                $mime = explode(';', $mime);
                return trim($mime[0]);
            }
            
        }

        return 'application/octet-stream';
    }
    
    
    /**
     * Decodes the response and returns the appropriate value
     *
     * @param string $response      Response data from server. Must be JSON encoded.
     * @param string $returnBool    Whether or not to return boolean
     * @return mixed
     *
    **/
    public static function getResponse($response, $returnBool)
    {
        if (($response = json_decode($response, true)) === null)
        {
            return false;
        }
        
        if ($returnBool)
        {
            return static::getStatus($response);
        }
        
        return $response;
    }

}

?>