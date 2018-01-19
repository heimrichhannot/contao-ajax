# Change Log
All notable changes to this project will be documented in this file.

## [1.2.2] - 2018-01-19

### Fixed
- invalid parameter documenataion

### Added
- code style fixer, and adjusted code in `src`

### Changed
- LGPL-3.0+ to LGPL-3.0-or-later
- moved classes to `/src`

## [1.2.1] - 2018-01-04

#### Fixed
- output buffer cleaning leads to error in unit tests (`Responce->output()`)

## [1.2.0] - 2017-12-18

#### Added
- `$GLOBALS['TL_HOOKS']['beforeAjaxAction']` hook

## [1.1.9] - 2017-12-04

#### Added
- & as arg_seperator argument to http_build_query calls, due not all hosters do this by default

## [1.1.8] - 2017-04-21

#### Changed
- replaced `ob_clean()` with `ob_end_clean()` to completely wipe out the buffer before sending output

## [1.1.7] - 2017-04-12

#### Fixed
- gethostbyaddr(): Address is not a valid IPv4 or IPv6 address for token whitelist
- added `AjaxToken::getActiveToken()`

## [1.1.6] - 2017-04-11

#### Fixed
- fixed ajax token recreation in `Responses`

## [1.1.5] - 2017-04-10

#### Fixed
- remove ajax token only when output is valid response

## [1.1.4] - 2017-04-10

#### Changed
- do never show error messages within `Response::output()`

## [1.1.3] - 2017-04-10

#### Added
- `csrf_protection` (default: false) parameter support added to config for cross-origin violation protection 
- Provide unit testing support for `HeimrichHannot\Ajax\Response::send())` when `$GLOBALS['UNIT_TESTING'] = true;`

## [1.1.2] - 2017-04-05

#### Fixed
- `HeimrichHannot\Ajax\Response` did not return a valid JsonResponse, now extends `\Symfony\Component\HttpFoundation\JsonResponse`

## [1.1.1] - 2017-03-31

#### Fixed
- Return only result and message within `HeimrichHannot\Ajax\Response`

## [1.1.0] - 2017-03-29

#### Changed
- All Requests and Responses make now usage of `\Symfony\Component\HttpFoundation` to provide unit test handling, removed all `header()` calls

#### Added 
- phpunit support added for Response Classes, simply set $GLOBALS variable with `define('UNIT_TESTING', true);` in your unittest bootstrap.php 

## [1.0.16] - 2017-03-27

#### Changed
- On ajax request in front end mode where request token expired, create a new request token and added `Ajax::isRequestTokenExpired()` to check within your implementation
- AjaxAction: getArguments() now supports regular expressions

## [1.0.15] - 2017-01-13

#### Fixed
- fixed readme
- fixed constant namespace issue

## [1.0.14] - 2016-12-02

#### Fixed
- check for public access modifiert within ajax callback function and die request with 400: Bad Request, the called method is not public.
