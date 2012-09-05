<?php

set_time_limit(0);

require 'PutIO/Autoloader.php';

$putio = new PutIO\API('AXNO3CVG');
// $putio->setHTTPEngine('Native');

print_r($putio->transfers->add('http://ecx.images-amazon.com/images/I/613-LbN%2BfaL._SL500_AA280_.jpg'));