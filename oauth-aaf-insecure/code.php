<?php
  require_once('../oauth-aaf/include/config.php');
  require_once('../oauth-aaf/include/init.php');

   if(isset($_GET['code']))
   {
     $code = $_GET['code'];

     // we persisted the random state var when we called login
     // see if they match with the returned state from OAuth
     $stored_state = $redis->get($code);
     $returned_state = $_GET['state'];

    // if the random state variable requested matches the one we're using to retreive then we can return credentials
    if($stored_state == $returned_state)
    {
      echo get_aaf_auth_attrs_json($redis, $code, $ATTR_KEYS);
    }
    else
    {
    // or states don't match (forgery?) but we don't care the reason
      echo json_encode(array('error' => 'Authorization request expired. Please try again.'));
    }

   }
?>
