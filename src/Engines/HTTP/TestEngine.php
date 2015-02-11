<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 */
namespace PutIO\Engines\HTTP;

use PutIO\Engines\HTTPEngine;
use PutIO\Helpers\HTTP\HTTPHelper;

/**
 * Class TestEngine
 * @package PutIO\Engines\HTTP
 */
final class TestEngine extends HTTPHelper implements HTTPEngine
{
    /**
     * @param string $method       HTTP request method. Only POST and GET.
     * @param string $url          Remote path to API module.
     * @param array  $params       Variables to be sent.
     * @param string $outFile      If $outFile is set, the response will be
     *                                  written to this file instead of StdOut.
     * @param bool   $returnBool
     * @param string $arrayKey     Will return all data on a specific array
     *                                  key of the response.
     * @param bool   $verifyPeer   If true, will use proper SSL peer/host
     *                                  verification.
     * @return mixed
     * @throws \PutIO\Exceptions\LocalStorageException
     */
    public function request($method, $url, array $params = [], $outFile = '', $returnBool = \false, $arrayKey = '', $verifyPeer = \true)
    {
        $url = str_replace('/', '_', $url);

        if (!$response = @file_get_contents("tests/data/{$url}.json")) {
            return \false;
        }

        return $this->getResponse($response, $returnBool, $arrayKey);
    }
}
