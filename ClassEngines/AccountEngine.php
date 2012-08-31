<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * This class manages anything related to 'Account'.
 * For precise return values see put.io's documentation here:
 *
 * https://api.put.io/v2/docs/#account
 *
**/

class AccountEngine extends ClassEngine
{
    
    /**
     * Returns an array of information about your account.
     *
     * @retun array
     *
    **/
    public function info()
    {
        return $this->get('account/info');
    }
    
    
    /**
     * Returns an array containing your account settings.
     *
     * @return array
     *
    **/
    public function settings()
    {
        return $this->get('account/settings');
    }
}

?>