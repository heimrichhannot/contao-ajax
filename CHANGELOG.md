# Change Log
All notable changes to this project will be documented in this file.

## [1.1.1] - 2017-03-31

### Fixed
- Return only result and message within `HeimrichHannot\Ajax\Response`

## [1.1.0] - 2017-03-29

### Changed
- All Requests and Responses make now usage of `\Symfony\Component\HttpFoundation` to provide unit test handling, removed all `header()` calls

### Added 
- phpunit support added for Response Classes, simply set $GLOBALS variable with `define('UNIT_TESTING', true);` in your unittest bootstrap.php 

## [1.0.16] - 2017-03-27

### Changed
- On ajax request in front end mode where request token expired, create a new request token and added `Ajax::isRequestTokenExpired()` to check within your implementation
- AjaxAction: getArguments() now supports regular expressions

## [1.0.15] - 2017-01-13

### Fixed
- fixed readme
- fixed constant namespace issue

## [1.0.14] - 2016-12-02

### Fixed
- check for public access modifiert within ajax callback function and die request with 400: Bad Request, the called method is not public.
