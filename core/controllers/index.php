<?php
namespace controllers;

class index extends controller implements \interfaces\controller{

    private $model;

    function __construct() {
        $this->model = new \models\compiladeiro();
    }

    public function init(){
        echo "ok";
        //$this->view('index');
    }
}
