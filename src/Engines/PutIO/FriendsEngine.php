<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 *
 * This class manages anything related to 'Friends'.
 * For precise return values see put.io's documentation here:
 *
 * https://api.put.io/v2/docs/#friends
 */
namespace PutIO\Engines\PutIO;

use PutIO\Helpers\PutIO\PutIOHelper;

/**
 * Class FriendsEngine
 * @package PutIO\Engines\PutIO
 */
final class FriendsEngine extends PutIOHelper
{
    /**
     * Returns an array of all friends.
     *
     * @return array 
     */
    public function listall()
    {
        return $this->get('friends/list', [], \false, 'friends');
    }
    
    /**
     * Returns an array of pending requests.
     *
     * @return array
     */
    public function pendingRequests()
    {
        return $this->get('friends/waiting-requests', [], \false, 'friends');
    }
    
    /**
     * Sends out a friend request to a specific user.
     *
     * @param string $username User to receive friend request
     * @return boolean
     */
    public function sendRequest($username)
    {
        return $this->post("friends/{$username}/request", [], \true);
    }
    
    /**
     * Approves a specific friend request.
     *
     * @param string $username User to have their request denied
     * @return boolean
     */
    public function approveRequest($username)
    {
        return $this->post("friends/{$username}/approve", [], \true);
    }

    /**
     * Denies a specific friend request.
     *
     * @param string $username User to have their request denied
     * @return boolean
     */
    public function denyRequest($username)
    {
        return $this->post("friends/{$username}/deny", [], \true);
    }

    /**
     * Unfriend someone.
     *
     * @param string $username User to have their request denied
     * @return boolean
     */
    public function unfriend($username)
    {
        return $this->post("friends/{$username}/unfriend", [], \true);
    }
}
