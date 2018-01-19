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
	public function __construct($message = '')
	{
		parent::__construct($message);
        $this->setStatusCode(Response::HTTP_BAD_REQUEST);
	}
}