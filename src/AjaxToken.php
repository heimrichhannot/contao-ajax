<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\Ajax;

use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Request\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class AjaxToken
{
    /**
     * Constants.
     */
    const SESSION_KEY = 'AJAX_TOKENS';
    /**
     * Object instance (Singleton).
     *
     * @var \RequestToken
     */
    protected static $objInstance;

    /**
     * Tokens.
     *
     * @var array
     */
    protected static $arrTokens;

    /**
     * Current session object.
     *
     * @var Session
     */
    protected $objSession;

    /**
     * Load the token or generate a new one.
     */
    protected function __construct()
    {
        if (!Request::getInstance()->hasSession()) {
            $this->objSession = new Session(new PhpBridgeSessionStorage());
            $this->objSession->start();
        } else {
            $this->objSession = Request::getInstance()->getSession();
        }

        static::$arrTokens = $this->objSession->get(static::SESSION_KEY);

        // Generate a new token if none is available
        if (empty(static::$arrTokens) || !\is_array(static::$arrTokens)) {
            static::$arrTokens[] = md5(uniqid(mt_rand(), true));
            $this->objSession->set(static::SESSION_KEY, static::$arrTokens);
        }
    }

    /**
     * Prevent cloning of the object (Singleton).
     */
    final public function __clone()
    {
    }

    /**
     * Return the tokens.
     *
     * @return array The request token
     */
    public static function get()
    {
        return static::$arrTokens;
    }

    /**
     * Remove a used token.
     *
     * @param $strToken
     */
    public function remove($strToken)
    {
        Arrays::removeValue($strToken, static::$arrTokens);

        $this->objSession->set(static::SESSION_KEY, static::$arrTokens);
    }

    /**
     * Create a new token.
     *
     * @return string The created request token
     */
    public function create()
    {
        $strToken = md5(uniqid(mt_rand(), true));
        static::$arrTokens[] = $strToken;

        $this->objSession->set(static::SESSION_KEY, static::$arrTokens);

        return $strToken;
    }

    /**
     * Return the valid active token.
     *
     * @return mixed|null the active token if valid, otherwise null
     */
    public function getActiveToken()
    {
        $strToken = Request::getGet(Ajax::AJAX_ATTR_TOKEN);

        if ($strToken && $this->validate($strToken)) {
            return $strToken;
        }

        return null;
    }

    /**
     * Validate a token.
     *
     * @param string $strToken The ajax token
     *
     * @return bool True if the token matches the stored one
     */
    public function validate($strToken)
    {
        // Validate the token
        if ('' !== $strToken && \in_array($strToken, static::$arrTokens, true)) {
            return true;
        }

        // Check against the whitelist (thanks to Tristan Lins) (see #3164)
        if (\Config::get('requestTokenWhitelist') && $_SERVER['REMOTE_ADDR']) {
            $strHostname = @gethostbyaddr($_SERVER['REMOTE_ADDR']);

            if ($strHostname) {
                foreach (\Config::get('requestTokenWhitelist') as $strDomain) {
                    if ($strDomain === $strHostname || preg_match('/\.'.preg_quote($strDomain, '/').'$/', $strHostname)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Return the object instance (Singleton).
     *
     * @return AjaxToken The object instance
     */
    public static function getInstance()
    {
        if (null === static::$objInstance) {
            static::$objInstance = new static();
        }

        return static::$objInstance;
    }
}
