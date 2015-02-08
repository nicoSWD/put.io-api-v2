<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 */
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Europe/Madrid');
}

require __DIR__ . '/../src/Autoloader.php';
