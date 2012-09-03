<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * Handles HTTP requests using native functions.
 *
**/

namespace PutIO\Engines\HTTP;

use PutIO\ClassEngine;
use PutIO\Interfaces\HTTPEngine;
use PutIO\Exceptions\RemoteConnectionException;
use PutIO\Exceptions\LocalStorageException;
use PutIO\Exceptions\FileNotFoundException;


class Native implements HTTPEngine
{
    
    /**
     * Makes an HTTP request to put.io's API and returns the response. Relies on native
     * PHP functions.
     *
     * NOTE!! Due to restrictions, files must be loaded into the memory when uploading.
     * I don't recommend uploading large files using native functions.
     *
     * Downloading is no issue as long as you're saving the file somewhere on the file system
     * rather than the memory. Set $outFile and you're all set!
     *
     * Returns false if a file was not found.
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
        if (isset($params['file']) AND $params['file'][0] === '@')
        {
            $file = substr($params['file'], 1);
            unset($params['file']);
            
            if (!$fileData = @file_get_contents($file))
            {
                throw new LocalStorageException('Unable to open local file: ' . basename($file));
            }
            
            $data = '';
            $boundary = '---------------------' . substr(md5($file . uniqid('', true)), 0, 10);
            
            foreach ($params AS $key => $value)
            {
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
            unset($fileData);
        }
        else
        {
            $data = http_build_query($params, '', '&');
            $contentType = 'application/x-www-form-urlencoded';
        }
        
        if ($method === 'POST')
        {
            $contextOptions = array(
                'http' => array(
                    'method' => 'POST',
                    'header' =>
                        "Content-type: " . $contentType . "\r\n" .
                        "Content-Length: " . strlen($data) . "\r\n" .
                        "User-Agent: nicoswd-putio/2.0\r\n",
                    'content' => $data
                )
            );
        }
        else
        {
            $contextOptions = array();
            $url .= '?' . $data;
        }

        $context = stream_context_create($contextOptions);
        
        if (($fp = @fopen($url, 'r', false, $context)) === false)
        {
            if (isset($http_response_header) AND preg_match('~HTTP/1.1\s+(\d+)~', $http_response_header[0], $match))
            {
                $responseCode = (int) $match[1];
                
                if ($responseCode === 404)
                {
                    // throw new FileNotFoundException('File not found.');
                    return false;
                }
            }
            else
            {
                throw new RemoteConnectionException('Unable to connect to remote resource.');
            }
        }
        
        // Save response to a local file
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
    
    
    /**
     * Attemps to get the MIME type of a given file.
     * 
     * Relies on the file info extension, which is shipped with PHP 5.3
     * and enabled by default. So,... nothing should go wrong, RIGHT?
     *
     * @param string $file   Path of the file you want to get the MIME type of.
     * @return string
     *
    **/
    protected function getMIMEType($file)
    {
        $magicFile = __DIR__ . '/etc/magic';
        
        if (function_exists('finfo_open') AND $info = @finfo_open(FILEINFO_MIME, $magicFile))
        {
            if (!$mime = @finfo_file($info, $file))
            {
                $mime = explode(';', $mime);
                return trim($mime[0]);
            }
        }
        
        return 'application/octet-stream';
    }
}


?>