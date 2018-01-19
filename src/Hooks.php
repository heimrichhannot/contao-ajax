<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Ajax;


use HeimrichHannot\Ajax\Response\ResponseRedirect;
use HeimrichHannot\Request\Request;

class Hooks
{
    /**
     * Contao initialize.php hook before request token validation happend
     */
    public function initializeSystemHook()
    {
        if (TL_MODE == 'BE')
        {
            return;
        }

        if (!Request::getInstance()->isXmlHttpRequest())
        {
            return;
        }

        // improved REQUEST_TOKEN handling within front end mode
        if (Request::getInstance()->isMethod('POST') && !\RequestToken::validate(Request::getPost('REQUEST_TOKEN')))
        {
            Ajax::setRequestTokenExpired();
        }
    }
}