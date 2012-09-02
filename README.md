Put.io OAuth API Wrapper written in PHP
=======================================
**(OAuth/API Version 2)**

A simple, but powerful PHP class for [put.io](https://put.io/)'s [OAuth API](https://api.put.io/v2/docs/) (version 2).
Supports all features that put.io's API provides, including file uploads, downloads, transfers, friends, etc...

Requires PHP 5.3, and the following PHP extensions: [cURL](http://php.net/book.curl) and [JSON](http://php.net/book.json).

Take a look at the [Wiki](https://github.com/nicoSWD/put.io-api-v2/wiki/) and [put.io's API documentation](https://api.put.io/v2/docs/) to get started.

**Pull requests are welcome!**

You can also find me on Twitter: @[nicoSWD](https://twitter.com/nicoSWD)


EXAMPLES
========

<pre><code>
require 'PutIO/API.php';
$putio = new PutIO\API($access_token);

// Retrieve a an array of files on your account.
$files = $putio->files->listall();

// Upload a file.
$file = 'path/to/file.jpg';
$putio->files->upload($file);

// Download a file.
$fileID = 1234;
$saveAs = 'my-file.jpg';
$putio->files->download($fileID, $saveAs);

// Search for files you have access to.
$query = 'my file';
$files = $putio->files->search($query);

// Add a new transfer (file or torrent)
$url = 'http://torrent.site.com/legal_video.torrent';
$putio->transfers->add($url);

// Get status of a transfer
$transferID = 1234;
$info = $putio->transfers->info($transferID);

// And a lot more...
</code></pre>


LICENSE
=======
Copyright (C) 2012  Nicolas Oelgart

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.