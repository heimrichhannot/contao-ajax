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


class ResponseData implements \JsonSerializable
{
	/**
	 * @var array $data
	 */
	protected $data;
	
	/**
	 * @var string $html
	 */
	protected $html;
	
	public function __construct($html='', $data=array())
	{
		$this->data = $data;
		$this->html = $html;
	}
	
	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * @param array $data
	 */
	public function setData(array $data)
	{
		$this->data = $data;
	}
	
	/**
	 * @return mixed
	 */
	public function getHtml()
	{
		return $this->html;
	}
	
	/**
	 * @param mixed $html
	 */
	public function setHtml($html)
	{
		$this->html = $html;
	}
	
	public function jsonSerialize()
	{
		return get_object_vars($this);
	}
	
}