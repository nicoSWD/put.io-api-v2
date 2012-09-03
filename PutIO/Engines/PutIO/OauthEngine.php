<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * This class manages anything related to 'OAuth'.
 * For precise return values see put.io's documentation here:
 *
 * https://api.put.io/v2/docs/#authentication
 *
**/

namespace PutIO\Engines\PutIO;
use PutIO\Engines\ClassEngine;


class OauthEngine extends ClassEngine
{
    
    /**
     * Sets the OAuth token for later access.
     *
     * @param string $oauthToken   User's OAuth token.
     * @return void
     *
    **/
    public function setOAuthToken($oauthToken)
    {
        $this->putio->oauthToken = $oauthToken;
    }
    
    
    /**
     * Returns a previously stored access token.
     *
     * @return string
     *
    **/
    public function getOAuthToken()
    {
        return $this->putio->oauthToken;
    }
    
    
    /**
     * Redirects the user to put.io where they have to give your app access permission.
     *
     * @param integer $clientID      Your app's client ID. You can find it here: https://put.io/v2/oauth2/applications
     * @param string  $redirectURI   The URI where the user will be redirected to once permission is granted.
     *
    **/
    public function requestPermission($clientID, $redirectURI)
    {
        header('Location: https://api.put.io/v2/oauth2/authenticate?' .
            'client_id=' . $clientID . '&' . 
            'response_type=code&' .
            'redirect_uri=' . rawurlencode($redirectURI)
        );
        
        exit;
    }
    
    
    /**
     * Second step of OAuth. This verifies the code obtained by the first function.
     * If valid, this function returns the user's access token, which you need to
     * save for all upcoming API requests.
     *
     * @param integer $clientID       App ID
     * @param string  $clientSecret   App secret
     * @param string  $redirectURI    Redirect URI
     * @param string  $code           Code obtained by first step
     * @return mixed
     *
    **/
    public function verifyCode($clientID, $clientSecret, $redirectURI, $code)
    {
        $response = $this->get('oauth2/access_token', array(
            'client_id'     => $clientID,
            'client_secret' => $clientSecret,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirectURI,
            'code'          => $code
        ));
        
        if (!empty($response['access_token']))
        {
            return $response['access_token'];
        }
        
        return false;
    }
}

?>