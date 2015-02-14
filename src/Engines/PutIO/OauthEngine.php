<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 *
 * This class manages anything related to 'OAuth'.
 * For precise return values see put.io's documentation here:
 *
 * https://api.put.io/v2/docs/#authentication
 */
namespace PutIO\Engines\PutIO;

use PutIO\Helpers\PutIO\PutIOHelper;

/**
 * Class OauthEngine
 * @package PutIO\Engines\PutIO
 */
final class OauthEngine extends PutIOHelper
{
    /**
     * Redirects the user to put.io where they have to give your app access
     * permission. Once permission is granted, the user will be redirected back
     * to the URL you specified in your app settings. On said page you have to
     * call self::verifyCode() to validate the user and get their access token. 
     *
     * @param int    $clientID      Your app's client ID. You can find it here:
     *                                  https://put.io/v2/oauth2/applications
     * @param string $redirectURI   The URI where the user will be redirected
     *                                  to once permission is granted.
     */
    public function requestPermission($clientID, $redirectURI)
    {
        header('Location: ' . $this->getRedirectURL($clientID, $redirectURI));
        exit;
    }
    
    /**
     * Second step of OAuth. This verifies the code obtained by the first
     * function. If valid, this function returns the user's access token, which
     * you need to save for all upcoming API requests.
     *
     * @param int    $clientID       App ID
     * @param string $clientSecret   App secret
     * @param string $redirectURI    Redirect URI
     * @param string $code           Code obtained by first step
     * @return string|bool
     */
    public function verifyCode($clientID, $clientSecret, $redirectURI, $code)
    {
        $response = $this->get('oauth2/access_token', [
            'client_id'     => $clientID,
            'client_secret' => $clientSecret,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirectURI,
            'code'          => $code
        ]);

        $result = \false;
        
        if (!empty($response['access_token'])) {
            $result = $response['access_token'];
        }
        
        return $result;
    }

    /**
     * @param int    $clientID
     * @param string $redirectURI
     * @return string
     */
    private function getRedirectURL($clientID, $redirectURI)
    {
        return sprintf(
            'https://api.put.io/v2/oauth2/authenticate?' .
            'client_id=%d&' .
            'response_type=code&' .
            'redirect_uri=%s',
            $clientID,
            rawurlencode($redirectURI)
        );
    }
}
