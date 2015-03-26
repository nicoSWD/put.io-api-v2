<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 *
 * This class enabled easy access to put.io's API (version 2)
 * Take a look at the Wiki for detailed instructions:
 * @see https://github.com/nicoSWD/put.io-api-v2/wiki
 *
 * Copyright (c) 2012-2015 Nicolas Oelgart nico@oelgart.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

namespace PutIO;

use PutIO\Engines;
use PutIO\Engines\HTTPEngine;
use PutIO\Helpers\PutIO\PutIOHelper;

/**
 * Class API
 * @package PutIO
 *
 * @property-read Engines\PutIO\AccountEngine $account
 * @property-read Engines\PutIO\FilesEngine $files
 * @property-read Engines\PutIO\FriendsEngine $friends
 * @property-read Engines\PutIO\OauthEngine $oauth
 * @property-read Engines\PutIO\TransfersEngine $transfers
 */
class API
{
    /**
     * @var string
     */
    const CLASS_VERSION = 'v0.3';

    /**
     * Holds the user's OAuth token.
     *
     * @var string
     */
    protected $OAuthToken = '';
    
    /**
     * Name of the HTTP engine. Possible options: Curl, Native
     * Defaults to cRUL and for a reason. Use cURL whenever possible.
     *
     * @var null|Engines\HTTPEngine
     */
    protected $HTTPEngine = \null;
    
    /**
     * If true (highly recommended), proper SSL peer/host verification
     * will be used.
     *
     * @var bool
     */
    protected $SSLVerifyPeer = \true;
 
    /**
     * Holds the instances of requested classes.
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Class constructor, sets the oauth token for later requests.
     *
     * @param string $OAuthToken   User's OAuth token.
     */
    public function __construct(string $OAuthToken = '')
    {
        $this->setOAuthToken($OAuthToken);
    }

    /**
     * @param bool $bool
     */
    public function setSSLVerifyPeer(bool $bool = \true)
    {
        $this->SSLVerifyPeer = $bool;
    }

    /**
     * @return bool $bool
     */
    public function getSSLVerifyPeer() : bool
    {
        return $this->SSLVerifyPeer;
    }

    /**
     * @param string $token
     */
    public function setOAuthToken(string $token)
    {
        $this->OAuthToken = $token;
    }

    /**
     * @return string
     */
    public function getOAuthToken() : string
    {
        return $this->OAuthToken;
    }

    /**
     * @param string|HTTPEngine $engine
     */
    public function setHTTPEngine($engine)
    {
        if (!($engine instanceof HTTPEngine)) {
            $class = '\PutIO\Engines\HTTP\\' . $engine . 'Engine';
            /* @var HTTPEngine $engine */
            $engine = new $class();
        }

        $this->HTTPEngine = $engine;
    }

    /**
     * @return HTTPEngine
     */
    public function getHTTPEngine() : HTTPEngine
    {
        if (!$this->HTTPEngine) {
            $engine = function_exists('curl_init')
                ? 'Curl'
                : 'Native';

            $engine = '\PutIO\Engines\HTTP\\' . $engine . 'Engine';
            $this->HTTPEngine = new $engine();
        }

        return $this->HTTPEngine;
    }
    
    /**
     * Magic method, returns an instance of the requested class.
     *
     * @param string $name   Class name
     * @return PutIOHelper
     * @throws \RuntimeException
     */
    public function __get(string $name) : PutIOHelper
    {
        $class = strtolower($name);
        $class = ucfirst($class) . 'Engine';
        
        if (!isset(static::$instances[$class])) {
            $className = __NAMESPACE__ . '\Engines\PutIO\\' . $class;

            if (!class_exists($className)) {
                throw new \RuntimeException("Unknown module '{$name}'");
            }

            static::$instances[$class] = new $className($this);
        }
        
        return static::$instances[$class];
    }
}
