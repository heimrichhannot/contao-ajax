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
	'HeimrichHannot\Ajax\Ajax'                        => 'system/modules/ajax/src/Ajax.php',
	'HeimrichHannot\Ajax\Hooks'                       => 'system/modules/ajax/src/Hooks.php',
	'HeimrichHannot\Ajax\AjaxAction'                  => 'system/modules/ajax/src/AjaxAction.php',
	'HeimrichHannot\Ajax\AjaxToken'                   => 'system/modules/ajax/src/AjaxToken.php',
	'HeimrichHannot\Ajax\Exception\AjaxExitException' => 'system/modules/ajax/src/Exception/AjaxExitException.php',
	'HeimrichHannot\Ajax\Response\ResponseRedirect'   => 'system/modules/ajax/src/Response/ResponseRedirect.php',
	'HeimrichHannot\Ajax\Response\Response'           => 'system/modules/ajax/src/Response/Response.php',
	'HeimrichHannot\Ajax\Response\Response404'        => 'system/modules/ajax/src/Response/Response404.php',
	'HeimrichHannot\Ajax\Response\ResponseData'       => 'system/modules/ajax/src/Response/ResponseData.php',
	'HeimrichHannot\Ajax\Response\ResponseError'      => 'system/modules/ajax/src/Response/ResponseError.php',
	'HeimrichHannot\Ajax\Response\ResponseSuccess'    => 'system/modules/ajax/src/Response/ResponseSuccess.php',
));
