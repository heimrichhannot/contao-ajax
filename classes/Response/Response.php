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

use HeimrichHannot\Ajax\Ajax;
use HeimrichHannot\Ajax\AjaxAction;
use HeimrichHannot\Ajax\AjaxToken;
use HeimrichHannot\Ajax\Exception\AjaxExitException;
use HeimrichHannot\Request\Request;

abstract class Response extends \Symfony\Component\HttpFoundation\JsonResponse implements \JsonSerializable
{
    protected $result;

    protected $message;

    protected $token;

    public function __construct($message = '')
    {
        parent::__construct($message);
        $this->message = $message;
        $this->token   = Request::getInstance()->get(Ajax::AJAX_ATTR_TOKEN);

        // create a new token for each response
        if ($this->token && !AjaxToken::getInstance()->validate($this->token))
        {
            $this->token = AjaxToken::getInstance()->create();
        }
    }

    /**
     * @return ResponseData
     */
    public function getResult()
    {
        return $this->result === null ? new ResponseData() : $this->result;
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

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function setCloseModal($blnClose = false)
    {
        $objResult             = $this->getResult();
        $arrData               = $objResult->getData();
        $arrData['closeModal'] = $blnClose;
        $objResult->setData($arrData);
        $this->setResult($objResult);
    }

    public function setUrl($strUrl)
    {
        $objResult      = $this->getResult();
        $arrData        = $objResult->getData();
        $arrData['url'] = $strUrl;
        $objResult->setData($arrData);
        $this->setResult($objResult);
    }

    public function getOutputData()
    {
        $objOutput          = new \stdClass();
        $objOutput->result  = $this->result;
        $objOutput->message = $this->message;
        $objOutput->token   = $this->token;

        return $objOutput;
    }

    /**
     * Output the response and clean output buffer
     */
    public function output()
    {
        ob_clean();

        $strBuffer = json_encode($this->getOutputData());

        $strBuffer = \Controller::replaceInsertTags($strBuffer, false); // do not cache inserttags

        $this->setJson($strBuffer);

        if (defined('UNIT_TESTING'))
        {
            throw new AjaxExitException($strBuffer, AjaxExitException::CODE_NORMAL_EXIT);
        }

        $this->send();
        exit;
    }

    public function send()
    {
        if (defined('UNIT_TESTING'))
        {
            throw new AjaxExitException(json_encode($this), AjaxExitException::CODE_NORMAL_EXIT);
        }

        return parent::send();
    }
}