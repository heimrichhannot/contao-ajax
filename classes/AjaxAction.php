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
	
	public static function generateUrl($strGroup, $strAction, array $arrAttributes = array(), $blnKeepParams = false, $strUrl = null)
	{
		global $objPage;
		
		if($strUrl === null)
		{
			$strUrl = $blnKeepParams ? null : \Controller::generateFrontendUrl($objPage->row(), null, null, true);
		}
		
		$strUrl = Url::addQueryString(Ajax::AJAX_ATTR_SCOPE . '=' . Ajax::AJAX_SCOPE_DEFAULT, $strUrl);
		$strUrl = Url::addQueryString(Ajax::AJAX_ATTR_GROUP . '=' . $strGroup, $strUrl);
		$strUrl = Url::addQueryString(Ajax::AJAX_ATTR_ACT . '=' . $strAction, $strUrl);
		
		foreach ($arrAttributes as $key => $attribute)
		{
			$strUrl = Url::addQueryString($key . '=' . $attribute, $strUrl);
		}
		
		return $strUrl;
	}
	
	public function call($objContext)
	{
		$objItem = null;
		
		if ($objContext === null) {
			header('HTTP/1.1 400 Bad Request');
			die('Bad Request, context not set.');
		}
		
		if (!method_exists($objContext, $this->strAction)) {
			header('HTTP/1.1 400 Bad Request');
			die('Bad Request, ajax method does not exist within context.');
		}
		
		return call_user_func_array(array($objContext, $this->strAction), $this->getArguments());
	}
	
	protected function getArguments()
	{
		$arrArgumentValues = array();
		$arrArguments      = $this->arrAttributes['arguments'];
		$arrOptional       = $this->arrAttributes['optional'];
		
		$strMethod = Request::getInstance()->getMethod();
		
		foreach ($arrArguments as $argument) {
			if (is_array($argument) || is_bool($argument)) {
				$arrArgumentValues[] = $argument;
				continue;
			}
			
			if (!in_array($argument, $arrOptional) && ($strMethod == 'POST' && !isset($_POST[$argument]) || $strMethod == 'GET' && !isset($_GET[$argument]))) {
				header('HTTP/1.1 400 Bad Request');
				die('Bad Request, missing argument ' . $argument);
			}
			
			
			$varValue = $strMethod == 'POST' ? Request::getPost($argument) : Request::getGet($argument);
			
			if ($varValue === 'true' || $varValue === 'false') {
				$varValue = filter_var($varValue, FILTER_VALIDATE_BOOLEAN);
			}
			
			$arrArgumentValues[] = $varValue;
		}
		
		return $arrArgumentValues;
	}
}