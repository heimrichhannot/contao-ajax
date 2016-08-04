<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'HeimrichHannot\Ajax\Ajax'                      => 'system/modules/ajax/classes/Ajax.php',
	'HeimrichHannot\Ajax\AjaxAction'                => 'system/modules/ajax/classes/AjaxAction.php',
	'HeimrichHannot\Ajax\Response\ResponseRedirect' => 'system/modules/ajax/classes/Response/ResponseRedirect.php',
	'HeimrichHannot\Ajax\Response\Response'         => 'system/modules/ajax/classes/Response/Response.php',
	'HeimrichHannot\Ajax\Response\ResponseData'     => 'system/modules/ajax/classes/Response/ResponseData.php',
	'HeimrichHannot\Ajax\Response\ResponseError'    => 'system/modules/ajax/classes/Response/ResponseError.php',
	'HeimrichHannot\Ajax\Response\ResponseSuccess'  => 'system/modules/ajax/classes/Response/ResponseSuccess.php',
));
