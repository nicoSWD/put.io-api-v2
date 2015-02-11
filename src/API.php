<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * This class enabled easy access to put.io's API (version 2)
 * Take a look at the Wiki for detailed instructions:
 * @see https://github.com/nicoSWD/put.io-api-v2/wiki
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace PutIO;

use PutIO\Engines;

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
     * Holds the instances of requested objects.
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Class constructor, sets the oauth token for later requests.
     *
     * @param string $OAuthToken   User's OAuth token.
     */
    public function __construct($OAuthToken = '')
    {
        $this->setOAuthToken($OAuthToken);
    }

    /**
     * @param bool $bool
     */
    public function setSSLVerifyPeer($bool = \true)
    {
        $this->SSLVerifyPeer = (bool) $bool;
    }

    /**
     * @return bool $bool
     */
    public function getSSLVerifyPeer()
    {
        return $this->SSLVerifyPeer;
    }

    /**
     * @param string $token
     */
    public function setOAuthToken($token)
    {
        $this->OAuthToken = (string) $token;
    }

    /**
     * @return string
     */
    public function getOAuthToken()
    {
        return $this->OAuthToken;
    }

    /**
     * @param string|Engines\HTTPEngine $engine
     */
    public function setHTTPEngine($engine)
    {
        if (!($engine instanceof Engines\HTTPEngine)) {
            $class = '\PutIO\Engines\HTTP\\' . $engine . 'Engine';
            /* @var Engines\HTTPEngine $engine */
            $engine = new $class();
        }

        $this->HTTPEngine = $engine;
    }

    /**
     * @return Engines\HTTPEngine
     */
    public function getHTTPEngine()
    {
        if (!$this->HTTPEngine) {
            if (function_exists('curl_init')) {
                $this->HTTPEngine = new Engines\HTTP\CurlEngine();
            } else {
                $this->HTTPEngine = new Engines\HTTP\NativeEngine();
            }
        }

        return $this->HTTPEngine;
    }
    
    /**
     * Magic method, returns an instance of the requested class.
     *
     * @param string $name   Class name
     * @return Helpers\PutIO\PutIOHelper
     * @throws \RuntimeException
     */
    public function __get($name)
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
