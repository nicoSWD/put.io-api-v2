## Put.io OAuth API Wrapper for PHP 5.4

[![Build Status](https://travis-ci.org/nicoSWD/put.io-api-v2.svg?branch=master)](https://travis-ci.org/nicoSWD/put.io-api-v2)

**(OAuth / API Version 2)**

This is a powerful PHP class for [put.io](https://put.io/)'s [OAuth2 API](https://api.put.io/v2/docs/) (version 2).
It supports all features that put.io's API provides, including file uploads, downloads, transfers, friends, etc...

Take a look at the [Wiki](https://github.com/nicoSWD/put.io-api-v2/wiki/) and [put.io's API documentation](https://api.put.io/v2/docs/) to get started.

**Pull requests are welcome! Fix, improve, suggest!**

You can also find me on Twitter: @[nicoSWD](https://twitter.com/nicoSWD)

## Install

Via Composer

``` bash
$ composer require "nicoswd/putio": "0.3.*@dev"
```

## Examples

```php
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

## Testing

``` bash
$ phpunit
```

## License
Copyright (C) 2012-2015 Nicolas Oelgart

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
