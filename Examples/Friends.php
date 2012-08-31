<?php

/**
 * This is an advanced example. Make sure you've seen 'FirstAuth.php' and 'Basics.php'
 * before!
 *
**/

require 'PutIO.php';

// Grab the user's access token from the database or wherever else you stored it.
$accessToken = 'A000000Z'; 
$putio = new PutIO($accessToken);

// Get a list of current 'friends':
$friends = $putio->friends->listall();
print_r($friends);

// Or a list of pending requests:
$pendingRequests = $putio->friends->pendingRequests();
print_r($pendingRequests);

// Send a friend request to someone:
$response = $putio->friends->sendRequest('nic0');
print_r($pendingRequests);

// Or deny someone's request:
$response = $putio->friends->denyRequest('nic0');
print_r($pendingRequests);

// NOTE: Accepting friends is not supported by put.io, probably to avoid
// spam or automated bot nets.

?>