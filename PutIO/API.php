<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * This class enabled easy access to put.io's API (version 2)
 * Take a look at the Wiki for detailed instructions:
 * https://github.com/nicoSWD/put.io-api-v2/wiki
 *
 * @license
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
 *
**/

namespace PutIO;


class API
{
    
    /**
     * Holds the instances of requested objects.
     *
    **/
    protected static $instances = array();
    
  
    /**
     * Holds the user's OAuth token.
     *
    **/
    public $oauthToken = '';
    
    
    /**
     * Name of the HTTP engine. Possible options: Curl, Native
     * Defaults to cRUL and for a reason. Use cURL whenever possible.
     *
    **/
    public $httpEngine = 'Curl';
 
    
    /**
     * Class constructor, sets the oauth token for later requests.
     *
     * @param string $oauthToken   User's OAuth token.
     * @return void
    **/
    public function __construct($oauthToken = '')
    {
        $this->oauthToken = $oauthToken;
    }
    
    
    /**
     * Changes the HTTP engine. As of now, only 'Curl' and 'Native' are
     * valid options. If you have cURL installed, I encourage you to use
     * it rather than the native option.
     *
     * @param string $name    Name of the HTTP engine.
     * @return void
     *
    **/
    public function setHTTPEngine($name)
    {
        $this->httpEngine = $name;
    }
    
    
    /**
     * Magic method, returns an instance of the requested class.
     *
     * @param string $name   Class name
     * @return object
     *
    **/
    public function __get($name)
    {
        $class = strtolower($name);
        $class = ucfirst($class) . 'Engine';
        
        if (!isset(static::$instances[$class]))
        {
            $className = __NAMESPACE__ . '\Engines\PutIO\\' . $class;
            static::$instances[$class] = new $className($this);
        }
        
        return static::$instances[$class];
    }
}

?>