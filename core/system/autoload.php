<?php

function autoload($classe){
    $name = explode("\\", $classe);
    if(count($name) == 2){
        $path = DIR_CORE . strtolower($name[0]) . '/' . strtolower($name[1]) . '.php';
    }else{
        $path = DIR_CORE . 'system/' . $classe . '.php';
    }

    if(!file_exists($path)){
        throw new Exception('Controller ' . $name[1] . ' not found');
    }

    require_once($path);
}
spl_autoload_register("autoload");
