oauth-2-shib-server
===================

Protect this OAuth2 server with Shibboleth (ie Australian Access Federation) and use client apps to authorize against it

Works with clients such as the MyTardis AAF Auth plugin: https://github.com/steveandroulakis/mytardis-app-auth-aaf

## Pre-requisites

An auth server is a complex thing, and there are several dependencies.

* The server must be a Shibboleth Service Provider. [This guide shows you how to be one for the Australian Access Federation.](http://wiki.aaf.edu.au/tech-info/sp-install-guide)
* mod_shib for Apache to protect URLs (or nginx equivalent, see [This guide](http://davidjb.com/blog/2013/04/integrating-nginx-and-a-shibboleth-sp-with-fastcgi/)
* [Composer](http://getcomposer.org/) package manager for php
* Install this [https://github.com/bshaffer/oauth2-server-php/](php OAuth2 server) using Composer
* MySQL with [this db / table defined](https://github.com/bshaffer/oauth2-server-php/#define-your-schema)
* Install [Predis](https://github.com/nrk/predis) (Redis data structure store API for PHP) via Composer
* [Redis](http://redis.io/) data structure store to temporarily hold credentials

