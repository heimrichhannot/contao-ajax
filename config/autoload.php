<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
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
	'HeimrichHannot\Ajax\Ajax'                        => 'system/modules/ajax/classes/Ajax.php',
	'HeimrichHannot\Ajax\Hooks'                       => 'system/modules/ajax/classes/Hooks.php',
	'HeimrichHannot\Ajax\AjaxAction'                  => 'system/modules/ajax/classes/AjaxAction.php',
	'HeimrichHannot\Ajax\Exception\AjaxExitException' => 'system/modules/ajax/classes/Exception/AjaxExitException.php',
	'HeimrichHannot\Ajax\Response\ResponseRedirect'   => 'system/modules/ajax/classes/Response/ResponseRedirect.php',
	'HeimrichHannot\Ajax\Response\Response'           => 'system/modules/ajax/classes/Response/Response.php',
	'HeimrichHannot\Ajax\Response\Response404'        => 'system/modules/ajax/classes/Response/Response404.php',
	'HeimrichHannot\Ajax\Response\ResponseData'       => 'system/modules/ajax/classes/Response/ResponseData.php',
	'HeimrichHannot\Ajax\Response\ResponseError'      => 'system/modules/ajax/classes/Response/ResponseError.php',
	'HeimrichHannot\Ajax\Response\ResponseSuccess'    => 'system/modules/ajax/classes/Response/ResponseSuccess.php',
));
