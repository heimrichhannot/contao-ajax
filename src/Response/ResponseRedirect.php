<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\Ajax\Response;

class ResponseRedirect extends Response
{
    public function __construct($message = '')
    {
        parent::__construct($message);
        $this->setStatusCode(Response::HTTP_MULTIPLE_CHOICES); // use 300 instead of 301 here, otherwise ie wont allow response payload
    }
}
