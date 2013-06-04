<?php
// By Steve Androulakis (steve.androulakis@gmail.com)
// Oh gosh why am I doing php again?
require_once('include/config.php');
require_once('include/init.php');

// validate the authorize request
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

// display an authorization form
if (empty($_POST) && !$BYPASS_AUTH_CONFIRM) {
  exit('
<form method="post">
  <label>Do You Authorize TestClient?</label><br />
  <input type="submit" name="authorized" value="yes">
  <input type="submit" name="authorized" value="no">
</form>');
}

$is_authorized = ($_POST['authorized'] === 'yes' || $BYPASS_AUTH_CONFIRM);
$server->handleAuthorizeRequest($request, $response, $is_authorized);
if ($is_authorized) {
  // get auth code and state info from returned OAuth2 header
  $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5);
  $codearr = explode('&state=', $code);
  $code = $codearr[0];
  $state = $codearr[1];

  // get shibboleth headers
  // assumes you're passing this authorize url through something like mod_shibboleth in apache first..
  $mail = $_SERVER['mail'];
  $cn = $_SERVER['cn'];

  // set shibboleth headers for temporary storing
  $aaf_attr['mail'] = $mail;
  $aaf_attr['cn'] = $cn;
  

  // temporarily store attributes from key in redis store for retrieval by client app
  set_auth_attrs($redis, $code, $aaf_attr, $REDIS_EXPIRE_SECONDS);
  
  // set received state to match with client app
  $redis->set($code, $state);
  $redis->expire($code, $REDIS_EXPIRE_SECONDS);
}
$response->send();
?>
