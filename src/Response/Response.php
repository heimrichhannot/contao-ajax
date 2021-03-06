<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\Ajax\Response;

use HeimrichHannot\Ajax\Ajax;
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
        $this->token = AjaxToken::getInstance()->getActiveToken();

        // create a new token for each response
        if (null !== $this->token) {
            $this->token = AjaxToken::getInstance()->create();
        }
    }

    /**
     * @return ResponseData
     */
    public function getResult()
    {
        return null === $this->result ? new ResponseData() : $this->result;
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
        $objResult = $this->getResult();
        $arrData = $objResult->getData();
        $arrData['closeModal'] = $blnClose;
        $objResult->setData($arrData);
        $this->setResult($objResult);
    }

    public function setUrl($strUrl)
    {
        $objResult = $this->getResult();
        $arrData = $objResult->getData();
        $arrData['url'] = $strUrl;
        $objResult->setData($arrData);
        $this->setResult($objResult);
    }

    public function getOutputData()
    {
        $objOutput = new \stdClass();
        $objOutput->result = $this->result;
        $objOutput->message = $this->message;
        $objOutput->token = $this->token;

        return $objOutput;
    }

    /**
     * Output the response and clean output buffer.
     */
    public function output()
    {
        // The difference between them is ob_clean wipes the buffer then continues buffering,
        // whereas ob_end_clean wipes it, then stops buffering.
        if (!defined('UNIT_TESTING')) {
            ob_end_clean();
        }

        $strBuffer = json_encode($this->getOutputData());

        $strBuffer = \Controller::replaceInsertTags($strBuffer, false); // do not cache inserttags

        $this->setJson($strBuffer);

        $this->send();

        // do not display errors in ajax request, as the generated json will no longer be valid
        // error messages my occur, due to exit and \FrontendUser::destruct does no longer have a valid \Database instance
        ini_set('display_errors', 0);

        exit;
    }

    public function send()
    {
        if (defined('UNIT_TESTING')) {
            throw new AjaxExitException(json_encode($this), AjaxExitException::CODE_NORMAL_EXIT);
        }

        return parent::send();
    }
}
