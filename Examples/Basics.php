<?php

/**
 * You need the user's OAuth token for these examples.
 * See 'FirstAuth.php' for examples on how to get it.
 *
**/

require 'PutIO.php';

// Grab the user's access token from the database or wherever else you stored it.
$accessToken = 'A000000Z'; 

// You can set the access token in two different ways:
$putio = new PutIO($accessToken);
// ... or:
$putio->oauth->setOAuthToken($accessToken);
// (Pick one method, don't use both! First method is the recommended one!)

/**
 * Once you've set the token, you're good to go!
 *
 * For instance, let's take a look at your files:
 *
**/

$files = $putio->files->listall();
print_r($files);

/**
 * Or download a remote file:
 *
 * NOTE: Downloads may take a while, and you likely need to give PHP
 * a longer (infinite) timeout by calling set_time_limit(0); before
 * triggering the download.
 *
**/
$fileID = 1234;
$saveAs = 'my-file.jpg';

$putio->files->download($fileID, $saveAs);

/**
 * Or want to upload a file to your account? No problem!
 *
 * NOTE: Must be an absolute path to the file!
 * NOTE 2: Uploads may take a while, and you likely need to give PHP
 * a longer (infinite) timeout: set_time_limit(0); before
 * triggering the upload.
 *
**/
$file = '/path/to/my/File.jpg';
$putio->files->upload($file);

?>