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


abstract class Response implements \JsonSerializable
{
	protected $result;
	
	protected $message;
	
	public function __construct($message = '')
	{
		$this->message = $message;
		header('Content-Type: application/json; charset=' . \Config::get('characterSet'));
	}
	
	/**
	 * @return ResponseData
	 */
	public function getResult()
	{
		return $this->result;
	}
	
	/**
	 * @param ResponseData $result
	 */
	public function setResult(ResponseData $result)
	{
		$this->result = $result;
	}
	
	/**
	 * @return mixed
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * @param mixed $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}
	
	
	public function jsonSerialize()
	{
		return get_object_vars($this);
	}
	
}