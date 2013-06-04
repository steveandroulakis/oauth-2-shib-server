<?php
// Some convenience functions for storing credentials in Redis using Predis

Predis\Autoloader::register();

$redis = new Predis\Client();

function set_auth_attrs($redis, $code, $attrs, $expiry)
{
    foreach ($attrs as $key => $value)
    {
        $codekey = $code . ':' . $key;
        $redis->set($codekey, $value);
        if($expiry)
        {
            $redis->expire($codekey, $expiry);
        }
    }
}

function get_aaf_auth_attr($redis, $code, $attr)
{
    return $redis->get($code . ':' . $attr);
}

function get_aaf_auth_attrs_json($redis, $code, $attr_keys)
{
    $values = null;

    foreach ($attr_keys as $attr_key)
    {
        $values[$attr_key] = get_aaf_auth_attr($redis, $code, $attr_key);  
    }

    return json_encode($values);
}

?>
