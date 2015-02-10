<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * Helps handing HTTP requests.
 */
namespace PutIO\Helpers\HTTP;

use PutIO\Exceptions\MissingJSONException;
use \Services_JSON;

/**
 * Class HTTPHelper
 * @package PutIO\Helpers\HTTP
 */
class HTTPHelper
{
    /**
     * Holds whether or not the JSON PHP extension is available.
     *
     * @var bool|null
     */
    protected $jsonExt = \null;
    
    /**
     * Returns true if the server responded with status === OK.
     *
     * @param array $response    Response from remote server.
     * @return bool
     */
    protected function getStatus(array $response)
    {
        if (isset($response['status']) && $response['status'] === 'OK') {
            return \true;
        }
        
        return \false;
    }
    
    /**
     * Parses the response header from the server, fetches the HTTP status code, 
     * and returns it.
     *
     * @param array $headers    Array containing response headers
     * @return int
     */
    protected function getResponseCode(array $headers)
    {
        if (isset($headers[0]) &&
            preg_match('~HTTP/1\.[01]\s+(\d+)~', $headers[0], $match)) {
            return (int) $match[1];
        }
        
        return 0;
    }
    
    /**
     * Attempts to get the MIME type of a given file. Required for native file
     * uploads.
     * 
     * Relies on the file info extension, which is shipped with PHP 5.3
     * and enabled by default. So,... nothing should go wrong, RIGHT?
     *
     * @param string $file    Path of the file you want to get the MIME type of.
     * @return string
     */
    protected function getMIMEType($file)
    {
        $mime = 'application/octet-stream';

        if (function_exists('finfo_open') && $info = @finfo_open(FILEINFO_MIME)) {
            if (($mime = @finfo_file($info, $file)) !== \false) {
                $mime = explode(';', $mime);
                $mime = trim($mime[0]);
            }
        }

        return $mime;
    }
    
    /**
     * Decodes the response and returns the appropriate value
     *
     * @param string $response      Response data from server.
     * @param bool   $returnBool    Whether or not to return boolean
     * @param string $arrayKey      Will return all data on a specific array key
     *                                  of the response.
     * @return array|bool
     */
    protected function getResponse($response, $returnBool, $arrayKey = '')
    {
        $response = $this->jsonDecode($response);

        if ($response === \null) {
            return \false;
        }
        
        if ($returnBool) {
            return $this->getStatus($response);
        }
        
        if ($arrayKey) {
            if (isset($response[$arrayKey])) {
                return $response[$arrayKey];
            }
            return \false;
        }
        
        return $response;
    }
    
    /**
     * Decodes a JSON encoded string. Natively, or using the PEAR package.
     *
     * @param string $string   JSON encoded string
     * @return array|null
     */
    protected function jsonDecode($string)
    {
        if (!isset($this->jsonExt)) {
            $this->jsonExt = function_exists('json_decode');
        }

        if ($this->jsonExt) {
            $result = @json_decode($string, \true);

            if (!$result || JSON_ERROR_NONE !== json_last_error()) {
                $result = \null;
            }
        } else {
            $result = $this->jsonDecodePEAR($string);
        }

        return $result;
    }

    /**
     * @param $string
     * @return array|null
     */
    protected function jsonDecodePEAR($string)
    {
        if (!class_exists('Services_JSON')) {
            require __DIR__ . '/../../Engines/JSON/JSON.php';
        }

        $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        return $json->decode($string);
    }
}
