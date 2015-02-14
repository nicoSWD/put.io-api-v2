<?php

/**
 * PSR-0 compliant auto loader.
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 */
spl_autoload_register(function ($className) {
    if (strpos($className, 'PutIO') === 0) {
        $className = substr($className, 6);
        $className = ltrim($className, '\\');
        $fileName  = '';

        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        return @include $fileName;
    }
});
