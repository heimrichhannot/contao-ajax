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

class ResponseError extends Response
{
	protected $state = Response::STATE_ERROR;
	
	public function __construct($message = '')
	{
		parent::__construct($message);
		header('HTTP/1.1 400 Bad Request');
	}
	
}