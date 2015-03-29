<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 *
 * This class manages anything related to 'Account'.
 * For precise return values see put.io's documentation here:
 *
 * https://api.put.io/v2/docs/#account
 */
declare(strict_types=1);

namespace PutIO\Engines\PutIO;

use PutIO\Helpers\PutIO\PutIOHelper;

/**
 * Class AccountEngine
 * @package PutIO\Engines\PutIO
 */
final class AccountEngine extends PutIOHelper
{
    /**
     * Returns an array of information about your account.
     * False on error.
     *
     * @return array|bool
     */
    public function info()
    {
        return $this->get('account/info', [], false, 'info');
    }
    
    /**
     * Returns an array containing your account settings.
     * False on error.
     *
     * @return array|bool
     */
    public function settings()
    {
        return $this->get('account/settings', [], false, 'settings');
    }
}
