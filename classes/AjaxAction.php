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

use Haste\Util\Url;
use HeimrichHannot\Haste\Util\Classes;
use HeimrichHannot\Request\Request;

class AjaxAction
{
    protected $strGroup;

    protected $strAction;

    protected $arrAttributes;

    public function __construct($strGroup, $strAction, array $arrAttributes = array())
    {
        $this->strGroup      = $strGroup;
        $this->strAction     = $strAction;
        $this->arrAttributes = $arrAttributes;
    }

    public static function removeAjaxParametersFromUrl($strUrl)
    {
        return Url::removeQueryString(
            Classes::getConstantsByPrefixes('HeimrichHannot\Ajax\Ajax', array('AJAX_ATTR')),
            $strUrl
        );
    }

    public static function generateUrl($strGroup, $strAction = null, array $arrAttributes = array(), $blnKeepParams = true, $strUrl = null)
    {
        global $objPage;

        if ($strUrl === null)
        {
            $strUrl = $blnKeepParams ? null : \Controller::generateFrontendUrl($objPage->row(), null, null, true);
        }

        $strUrl = Url::addQueryString(http_build_query(static::getParams($strGroup, $strAction)), $strUrl);
        $strUrl = Url::addQueryString(http_build_query($arrAttributes), $strUrl);

        return $strUrl;
    }

    public static function getParams($strGroup, $strAction = null)
    {
        $arrParams = array
        (
            Ajax::AJAX_ATTR_SCOPE => Ajax::AJAX_SCOPE_DEFAULT,
            Ajax::AJAX_ATTR_GROUP => $strGroup,
        );

        if ($strAction !== null)
        {
            $arrParams[Ajax::AJAX_ATTR_ACT] = $strAction;
        }

        return $arrParams;
    }

    public function call($objContext)
    {
        $objItem = null;

        if ($objContext === null)
        {
            header('HTTP/1.1 400 Bad Request');
            die('Bad Request, context not set.');
        }

        if (!method_exists($objContext, $this->strAction))
        {
            header('HTTP/1.1 400 Bad Request');
            die('Bad Request, ajax method does not exist within context.');
        }

        $reflection = new \ReflectionMethod($objContext, $this->strAction);

        if (!$reflection->isPublic())
        {
            header('HTTP/1.1 400 Bad Request');
            die('Bad Request, the called method is not public.');
        }

        return call_user_func_array(array($objContext, $this->strAction), $this->getArguments());
    }

    protected function getArguments()
    {
        $arrArgumentValues = array();
        $arrArguments      = $this->arrAttributes['arguments'];
        $arrOptional       = is_array($this->arrAttributes['optional']) ? $this->arrAttributes['optional'] : array();

        $strMethod = Request::getInstance()->getMethod();

        foreach ($arrArguments as $argument)
        {
            if (is_array($argument) || is_bool($argument))
            {
                $arrArgumentValues[] = $argument;
                continue;
            }

            if (count(preg_grep('/' . $argument . '/i', $arrOptional)) < 1 && count(preg_grep('/' . $argument . '/i', array_keys($strMethod == 'POST' ? $_POST : $_GET))) < 1)
            {
                header('HTTP/1.1 400 Bad Request');
                die('Bad Request, missing argument ' . $argument);
            }


            $varValue = $strMethod == 'POST' ? Request::getPost($argument) : Request::getGet($argument);

            if ($varValue === 'true' || $varValue === 'false')
            {
                $varValue = filter_var($varValue, FILTER_VALIDATE_BOOLEAN);
            }

            $arrArgumentValues[] = $varValue;
        }

        return $arrArgumentValues;
    }
}
