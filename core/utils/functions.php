<?php

function debug($arr, $break = true){
    
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    
    if($break){
      exit;
    }
}

function toJson($rs, $die = true){
    
    if(!is_array($rs)){
        $rs = (array) $rs;
    }
    
    $json = json_encode($rs, JSON_NUMERIC_CHECK);
    
    if($die){
        echo $json;
        exit;
    }
    
    return $json;
}

function encodeByHash($d, $hashCode = 'LRE&VTISN@SMK') {
    $context = hash_init("md5", HASH_HMAC, $hashCode);
    hash_update($context, $d);
    return hash_final($context);
}