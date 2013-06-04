<?php 

OAuth2_Autoloader::register();

// create your storage again
$storage = new OAuth2_Storage_Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

// create your server again
$server = new OAuth2_Server($storage);

// Add the "Authorization Code" grant type (this is required for authorization flows)
$server->addGrantType(new OAuth2_GrantType_AuthorizationCode($storage));

$request = OAuth2_Request::createFromGlobals();
$response = new OAuth2_Response();

?>
