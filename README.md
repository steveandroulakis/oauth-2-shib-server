oauth-2-shib-server
===================

Protect this OAuth2 server with Shibboleth (ie Australian Access Federation) and use client apps to authorize against it.

Works with clients such as the MyTardis AAF Auth plugin: https://github.com/steveandroulakis/mytardis-app-auth-aaf

## Pre-requisites

An auth server is a complex thing, and there are several dependencies.

* The server must be a Shibboleth Service Provider. [This guide shows you how to be one for the Australian Access Federation.](http://wiki.aaf.edu.au/tech-info/sp-install-guide)
* mod_shib for Apache to protect URLs (or nginx equivalent, see [this guide](http://davidjb.com/blog/2013/04/integrating-nginx-and-a-shibboleth-sp-with-fastcgi/)).
* php
* [Composer](http://getcomposer.org/) package manager for php
* Install this [php OAuth2 server](https://github.com/bshaffer/oauth2-server-php/) using Composer
* MySQL with [this db / table defined](https://github.com/bshaffer/oauth2-server-php/#define-your-schema)
* Install [Predis](https://github.com/nrk/predis) (Redis data structure store API for PHP) via Composer
* [Redis](http://redis.io/) data structure store to temporarily hold credentials

## Configuration

* Create the database structure defined in the OAuth2 server above.
* Execute the following sql statement in your MySQL db

```sql
INSERT INTO oauth_clients (client_id, client_secret, redirect_uri)
  VALUES ("YOUR-TEST-CLIENT-NAME", "testpass", "http://YOUR-CLIENT-APP-URL/")
```

This is the url and a key for your client app that the OAuth2 server will redirect its auth code to.

* Clone this repository to the directory Apache is serving.
* Protect oauth-aaf/ with Shibboleth. For example, in `/etc/httpd/conf.d/shib.conf`

```conf
<Location /oauth-aaf>
  AuthType shibboleth
  ShibRequestSetting requireSession 1
  require valid-user
</Location>
```

This means that calls to authorize.php such as `oauth-aaf/authorize.php?response_type=code&client_id=test-client-1&state=c39ffae096f1b691dd5e78e48e06458c` will be intercepted by Shibboleth and make the user log in via their idP (ie via the Australian Access Federation).

* Edit `oauth-aaf/include/config.php`, setting your MySQL database credentials, and php dependency (Composer Autoloader) paths.
* Make sure your Redis server is running :)

## Usage

### Authorize from your client, and authenticate via Shibboleth

_Note: Each request to authorize.php from your client should have a randomly generated state string in the URL. You should keep this and use it along with your authorization code to retreive user credentials later on._

`A call to oauth-aaf/authorize.php` from your client app, such as the URL example above with your client ID and random state string will:
* Trigger mod_shib to ask the user for credentials, eg Australian Access Federation login
* Once authenticated, redirects to the client defined in your MySQL database. Shibboleth headers (at the moment, mail and common name - cn, will be stored temporarily on the server and you'll retreive them in the next step).

The OAuth2-generated Authorization Code and State will be included in the redirect and should be used to call `oauth-aaf-insecure/code.php` to receive a JSON string back with credentials. These credentials will be available temporarily (default 120 seconds in config.php).

### Use your Authorization Code to retrieve stored user credentials

Example client code, given an authorization_code and state redirected to us from authorize.php:

```php
$code = $_GET['code'];
$state = $_GET['state'];

// finds credentials for given authorization_code and matches state for anti-forgery
$url = "http://bdp-aaf-dev.dyndns.org/oauth-aaf-insecure/code.php?code=" . $code . "&state=" . $state;

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
$result = curl_exec($ch);
curl_close($ch);

echo $result
```

The result will take the form of:

```json
{
    "mail": "steve.androulakis@test.com",
    "cn": "Steve Androulakis"
}
```

Which should be used to create a user in your system, or start a user session (ie log them in).
