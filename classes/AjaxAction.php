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
use HeimrichHannot\Ajax\Response\Response;
use HeimrichHannot\Ajax\Response\ResponseError;
use HeimrichHannot\Haste\Util\Classes;
use HeimrichHannot\Request\Request;

class AjaxAction
{
    protected $strGroup;

    protected $strAction;

    protected $arrAttributes;

    protected $strToken;

    public function __construct($strGroup, $strAction, array $arrAttributes = [], $strToken = null)
    {
        $this->strGroup      = $strGroup;
        $this->strAction     = $strAction;
        $this->arrAttributes = $arrAttributes;
        $this->strToken      = $strToken;
    }

    public static function removeAjaxParametersFromUrl($strUrl)
    {
        return Url::removeQueryString(
            Classes::getConstantsByPrefixes('HeimrichHannot\Ajax\Ajax', ['AJAX_ATTR']),
            $strUrl
        );
    }

    public static function generateUrl($strGroup, $strAction = null, array $arrAttributes = [], $blnKeepParams = true, $strUrl = null)
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
        $arrParams = [
            Ajax::AJAX_ATTR_SCOPE => Ajax::AJAX_SCOPE_DEFAULT,
            Ajax::AJAX_ATTR_GROUP => $strGroup,
        ];

        if ($strAction !== null)
        {
            $arrParams[Ajax::AJAX_ATTR_ACT] = $strAction;
        }

        $arrConfig = $GLOBALS['AJAX'][$strGroup]['actions'][$strAction];

        if ($arrConfig && $arrConfig['csrf_protection'])
        {
            $strToken = Request::getGet(Ajax::AJAX_ATTR_TOKEN);

            // create a new token for each action
            if(!$strToken || ($strToken && !AjaxToken::getInstance()->validate($strToken)))
            {
                $arrParams[Ajax::AJAX_ATTR_TOKEN] = AjaxToken::getInstance()->create();
            }
        }

        return $arrParams;
    }

    public function call($objContext)
    {
        $objItem = null;

        // remove current used ajax token
        if($this->strToken !== null)
        {
            AjaxToken::getInstance()->remove($this->strToken);
        }

        if ($objContext === null)
        {
            $objResponse = new ResponseError('Bad Request, context not set.');
            $objResponse->send();
            exit;
        }

        if (!method_exists($objContext, $this->strAction))
        {
            $objResponse = new ResponseError('Bad Request, ajax method does not exist within context.');
            $objResponse->send();
            exit;
        }

        $reflection = new \ReflectionMethod($objContext, $this->strAction);

        if (!$reflection->isPublic())
        {
            $objResponse = new ResponseError('Bad Request, the called method is not public.');
            $objResponse->send();
            exit;
        }

        return call_user_func_array([$objContext, $this->strAction], $this->getArguments());
    }

    protected function getArguments()
    {
        $arrArgumentValues = [];
        $arrArguments      = $this->arrAttributes['arguments'];
        $arrOptional       = is_array($this->arrAttributes['optional']) ? $this->arrAttributes['optional'] : [];

        $strMethod = Request::getInstance()->getMethod();

        $arrCurrentArguments = Request::getInstance()->isMethod('POST') ? Request::getInstance()->request->all() : Request::getInstance()->query->all();

        foreach ($arrArguments as $argument)
        {
            if (is_array($argument) || is_bool($argument))
            {
                $arrArgumentValues[] = $argument;
                continue;
            }

            if (count(preg_grep('/' . $argument . '/i', $arrOptional)) < 1 && count(preg_grep('/' . $argument . '/i', array_keys($arrCurrentArguments))) < 1)
            {
                $objResponse = new ResponseError('Bad Request, missing argument ' . $argument);
                $objResponse->send();
                exit;
            }


            $varValue = Request::getInstance()->isMethod('POST') ? Request::getPost($argument) : Request::getGet($argument);

            if ($varValue === 'true' || $varValue === 'false')
            {
                $varValue = filter_var($varValue, FILTER_VALIDATE_BOOLEAN);
            }

            $arrArgumentValues[] = $varValue;
        }

        return $arrArgumentValues;
    }
}
