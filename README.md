## Put.io OAuth API Wrapper for PHP 5.4

[![Latest Stable Version](https://poser.pugx.org/nicoswd/putio/v/stable.svg)](https://packagist.org/packages/nicoswd/putio)
[![Build Status](https://scrutinizer-ci.com/g/nicoSWD/put.io-api-v2/badges/build.png?b=master)](https://scrutinizer-ci.com/g/nicoSWD/put.io-api-v2/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/nicoSWD/put.io-api-v2/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nicoSWD/put.io-api-v2/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nicoSWD/put.io-api-v2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nicoSWD/put.io-api-v2/?branch=master)

[![Put.io Logo](http://s15.postimg.org/j2ewelz5n/putio_logo.png)](https://put.io/)

This is a powerful PHP class for [put.io](https://put.io/)'s [OAuth2 API](https://api.put.io/v2/docs/) (version 2).
It supports all features that put.io's API provides, including file uploads, downloads, transfers, friends, etc...

Take a look at the [Wiki](https://github.com/nicoSWD/put.io-api-v2/wiki/) and [put.io's API documentation](https://api.put.io/v2/docs/) to get started.

Find me on Twitter: @[nicoSWD](https://twitter.com/nicoSWD)

## Install

Via Composer

``` bash
$ composer require "nicoswd/putio": "0.3*"
```

Via git
``` bash
$ git clone git@github.com:nicoSWD/put.io-api-v2.git
```


## Usage

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

## Security

If you discover any security related issues, please email nico@oelgart.com instead of using the issue tracker.

## Testing

``` bash
$ phpunit
```

## Contributing
Pull requests are very welcome! If they include tests, even better. This project follows PSR-2 coding standards, please make sure your pull requestst do too.

## License

[![License](https://poser.pugx.org/nicoswd/putio/license.svg)](https://packagist.org/packages/nicoswd/putio)
