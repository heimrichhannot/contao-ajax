<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\Ajax;

use HeimrichHannot\Request\Request;

class Hooks
{
    /**
     * Contao initialize.php hook before request token validation happend.
     */
    public function initializeSystemHook()
    {
        if (TL_MODE === 'BE') {
            return;
        }

        if (!Request::getInstance()->isXmlHttpRequest()) {
            return;
        }

        // improved REQUEST_TOKEN handling within front end mode
        if (Request::getInstance()->isMethod('POST') && !\RequestToken::validate(Request::getPost('REQUEST_TOKEN'))) {
            Ajax::setRequestTokenExpired();
        }
    }
}
