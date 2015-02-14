<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 */
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Europe/Madrid');
}

require __DIR__ . '/../src/PutIO/Autoloader.php';
