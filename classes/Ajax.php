<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Ajax;

use HeimrichHannot\Ajax\Response\Response;
use HeimrichHannot\Ajax\Response\ResponseError;
use HeimrichHannot\Request\Request;

class Ajax extends \Controller
{
    const AJAX_ATTR_SCOPE  = 'as';
    const AJAX_ATTR_ACT    = 'aa';
    const AJAX_ATTR_GROUP  = 'ag';
    const AJAX_ATTR_TYPE   = 'at';
    const AJAX_ATTR_AJAXID = 'aid';
    const AJAX_ATTR_TOKEN  = 'ato';

    const AJAX_SCOPE_DEFAULT = 'ajax';
    const AJAX_TYPE_MODULE   = 'module';

    const AJAX_ERROR_INVALID_GROUP        = 1;
    const AJAX_ERROR_NO_AVAILABLE_ACTIONS = 2;
    const AJAX_ERROR_INVALID_ACTION       = 3;
    const AJAX_ERROR_INVALID_TOKEN        = 4;


    /**
     * Object instance (Singleton)
     *
     * @var \Environment
     */
    protected static $objInstance;

    /**
     * Determine if the current ajax request is group related
     *
     * @param $strGroup The ajax group
     *
     * @return boolean True / False if group from request match, otherwise null (no ajax request)
     */
    public static function isRelated($strGroupRequested)
    {
        if (Request::getInstance()->isXmlHttpRequest())
        {
            if (($strGroup = static::getActiveGroup($strGroupRequested)) === null)
            {
                return false;
            }

            return true;
        }

        return null;
    }

    /**
     * Trigger a valid ajax action
     *
     * @param $strGroup
     * @param $strAction
     * @param $objContext
     */
    public static function runActiveAction($strGroup, $strAction, $objContext)
    {
        if (Request::getInstance()->isXmlHttpRequest())
        {
            /** @var AjaxAction */
            $objAction = Ajax::getActiveAction($strGroup, $strAction);

            if ($objAction === static::AJAX_ERROR_INVALID_GROUP)
            {
                $objResponse = new ResponseError('Invalid ajax group.');
                $objResponse->send();
                exit;
            }
            else
            {
                if ($objAction === static::AJAX_ERROR_NO_AVAILABLE_ACTIONS)
                {
                    $objResponse = new ResponseError('No available ajax actions within given group.');
                    $objResponse->send();
                    exit;
                }
                else
                {
                    if ($objAction === static::AJAX_ERROR_INVALID_ACTION)
                    {
                        $objResponse = new ResponseError('Invalid ajax act.');
                        $objResponse->send();
                        exit;
                    }
                    else if ($objAction === static::AJAX_ERROR_INVALID_TOKEN)
                    {
                        $objResponse = new ResponseError('Invalid ajax token.');
                        $objResponse->send();
                        exit;
                    }
                    else
                    {
                        if ($objAction !== null)
                        {
                            $objResponse = $objAction->call($objContext);

                            /** @var Response */
                            if ($objResponse instanceof Response)
                            {
                                $strToken = Request::getGet(static::AJAX_ATTR_TOKEN);

                                // remove used ajax tokens
                                if($strToken !== null)
                                {
                                    AjaxToken::getInstance()->remove($strToken);
                                }

                                $objResponse->output();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the active ajax action object
     *
     * @param string $strGroupRequested Requested ajax group
     *
     * @return string The name of the active group, otherwise null
     */
    public static function getActiveGroup($strGroupRequested)
    {
        $strScope = Request::getGet(static::AJAX_ATTR_SCOPE);
        $strGroup = Request::getGet(static::AJAX_ATTR_GROUP);

        if ($strScope !== static::AJAX_SCOPE_DEFAULT)
        {
            return null;
        }

        if (!$strGroup)
        {
            return null;
        }

        if ($strGroupRequested != $strGroup)
        {
            return null;
        }

        return $strGroup;
    }

    /**
     * Get the active ajax action object
     *
     * @param $strGroupRequested  Requested ajax group
     * @param $strActionRequested Requested ajax action within group
     *
     * @return AjaxAction|int  A valid AjaxAction | null if the action is not a registered ajax action
     */
    public static function getActiveAction($strGroupRequested, $strActionRequested)
    {
        $strAct   = Request::getGet(static::AJAX_ATTR_ACT);
        $strToken = Request::getGet(static::AJAX_ATTR_TOKEN);

        if (!$strAct)
        {
            return null;
        }

        if (($strGroup = static::getActiveGroup($strGroupRequested)) === null)
        {
            return null;
        }

        if ($strActionRequested != $strAct)
        {
            return null;
        }

        $arrConfig = $GLOBALS['AJAX'];

        if (!is_array($arrConfig))
        {
            return null;
        }

        if (!isset($arrConfig[$strGroup]))
        {
            return static::AJAX_ERROR_INVALID_GROUP;
        }

        if (!is_array($arrConfig[$strGroup]['actions']))
        {
            return static::AJAX_ERROR_NO_AVAILABLE_ACTIONS;
        }

        $arrActions = $arrConfig[$strGroup]['actions'];

        if (!array_key_exists($strActionRequested, $arrActions))
        {
            return static::AJAX_ERROR_INVALID_ACTION;
        }

        $arrAttributes = $arrActions[$strAct];

        // ajax request token check
        if ($arrAttributes['csrf_protection'] && (!$strToken || !AjaxToken::getInstance()->validate($strToken)))
        {
            return static::AJAX_ERROR_INVALID_TOKEN;
        }

        return new AjaxAction($strGroup, $strAct, $arrAttributes, $strToken);
    }

    /**
     * Set new request token and set expired state within $_POST as param REQUEST_TOKEN_EXPIRED
     */
    public static function setRequestTokenExpired()
    {
        \RequestToken::initialize();
        $_POST['REQUEST_TOKEN_EXPIRED'] = true;
        $_POST['REQUEST_TOKEN']         = \RequestToken::get();
        \Input::setPost('REQUEST_TOKEN', \RequestToken::get());
        \Input::setPost('REQUEST_TOKEN_EXPIRED', true);
        Request::getInstance()->request->set('REQUEST_TOKEN', \RequestToken::get());
        Request::getInstance()->request->set('REQUEST_TOKEN_EXPIRED', true);
    }

    /**
     * Return true if the request token has expired in between
     *
     * @return mixed
     */
    public static function isRequestTokenExpired()
    {
        return TL_MODE == 'FE' && Request::getInstance()->isXmlHttpRequest() && Request::getPost('REQUEST_TOKEN_EXPIRED');
    }


    /**
     * Prevent direct instantiation (Singleton)
     *
     */
    protected function __construct()
    {
    }


    /**
     * Prevent cloning of the object (Singleton)
     *
     */
    final public function __clone()
    {
    }


    /**
     * Return the object instance (Singleton)
     *
     * @return \Environment The object instance
     *
     */
    public static function getInstance()
    {
        if (static::$objInstance === null)
        {
            static::$objInstance = new static();
        }

        return static::$objInstance;
    }
}