<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Ajax\Response;

class ResponseRedirect extends Response
{
	
	public function __construct($message = '')
	{
		parent::__construct($message);
		header('HTTP/1.1 301 Moved Permanently');
	}
	
	
	public function setUrl($strUrl)
	{
		$this->setResult(new ResponseData('', array('url' => $strUrl)));
	}
}