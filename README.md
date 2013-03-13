Put.io OAuth API Wrapper written in PHP
=======================================
**(OAuth/API Version 2)**

[![endorse](http://api.coderwall.com/nicoswd/endorsecount.png)](http://coderwall.com/nicoswd)

This is a powerful PHP class for [put.io](https://put.io/)'s [OAuth2 API](https://api.put.io/v2/docs/) (version 2).
It supports all features that put.io's API provides, including file uploads, downloads, transfers, friends, etc...

It only requires PHP >=5.3! HTTP requests and JSON are supported natively if necessary!

If you have both, [cURL](http://php.net/book.curl) and the [JSON](http://php.net/book.json) PHP extension installed,
no configuration is required! **However**, if you're missing the cURL extension, you need to add one line of code:

```php
$putio->setHTTPEngine('Native');
```

**NOTE:** Only add this line if you really don't have cURL!

Secondly, if you're missing the JSON extension, you have to download the [Services_JSON](http://pear.php.net/package/Services_JSON/download) package from the Pear repo.
You can do that here:

http://pear.php.net/package/Services_JSON/download

Once downloaded, extract <code>JSON.php</code> from said package and place it into <code>PutIO/Engines/JSON/</code>, and you're good to go!

__That's all!__

Take a look at the [Wiki](https://github.com/nicoSWD/put.io-api-v2/wiki/) and [put.io's API documentation](https://api.put.io/v2/docs/) to get started.

**Pull requests are welcome! Fix, improve, suggest!**

You can also find me on Twitter: @[nicoSWD](https://twitter.com/nicoSWD)


Examples
========

```php
require 'PutIO/Autoloader.php';
$putio = new PutIO\API($accessToken);

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
```


License
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
