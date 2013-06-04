<?php 
// error reporting
//ini_set('display_errors',1);error_reporting(E_ALL);

date_default_timezone_set('Australia/Melbourne');

// Composer Autoloaders - set to your own paths once installed
require_once('/var/www/html/oauth/vendor/bshaffer/oauth2-server-php/src/OAuth2/Autoloader.php');
require_once('/var/www/html/oauth/vendor/predis/predis/lib/Predis/Autoloader.php');

// redis server initialization
require_once('../oauth-aaf-insecure/redis/init.php');

$dsn = "mysql:dbname=oauth_test;host=localhost";
$username = 'root';
$password = '';

// credentials are stored on server for x time before disappearing (to give clients enough time to get credentials back)
$REDIS_EXPIRE_SECONDS = 120;

// replace with the header keys you want back from shib
// TODO: Enable this functionality in the setting part not just getting
$ATTR_KEYS =  array('mail', 'cn');

// 'false' for default form asking if you want to authorize the passing of credentials to the client app - disabled by default since shibboleth already asks such a thing
$BYPASS_AUTH_CONFIRM = true;

?>
