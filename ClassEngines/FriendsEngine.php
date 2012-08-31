<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * This class manages anything related to 'Friends'.
 * For precise return values see put.io's documentation here:
 *
 * https://api.put.io/v2/docs/#friends
 *
**/

class FriendsEngine extends ClassEngine
{
    
    /**
     * Returns an array of all friends.
     *
     * @return array 
     *
    **/
    public function listall()
    {
        return $this->get('friends/list');
    }
    
    
    /**
     * Returns an array of pending requests.
     *
     * @return array
     *
    **/
    public function pendingRequests()
    {
        return $this->get('friends/requests');
    }
    
    
    /**
     * Sends out a friend request to a specific user.
     *
     * @param string $username User to receive friend request
     * @return array
     *
    **/
    public function sendRequest($username)
    {
        return $this->post('friends/' . $username . '/request');
    }
    
    
    /**
     * Denies a specific friend request.
     *
     * @param string $username User to have their request denied
     * @return array
     *
    **/
    public function denyRequest($username)
    {
        return $this->post('friends/' . $username . '/deny');
    }
}

?>