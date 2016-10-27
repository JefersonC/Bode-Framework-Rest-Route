<?php
namespace controllers;

class controller extends \filters\filter{

    public function view($name, $data = null, $template = true){

    	//debug($data);

        if($data != NULL){
            extract($data);
        }

        if ($template){
            require_once DIR_VIEWS . 'header.phtml';
        }

        $path = DIR_VIEWS . $name . '.phtml';
        if(file_exists($path)){
            require_once $path;
        }

        if ($template){
            require_once DIR_VIEWS . 'footer.phtml';
        }
    }

}
