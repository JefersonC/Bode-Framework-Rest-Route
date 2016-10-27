<?php

namespace controllers;

class build extends controller implements \interfaces\restControllerInterface {
    private $customer;
    private $user;
    
    private $dependencies = array(
        'business' => array()
    );
    
    function __construct() {
        $this->customer = new \models\customerModel();
        $this->user = new \models\userModel();
    }

    public function get($params = array()) {
        debug($params);
        exit;
    }

    public function put($params = array()) {
        exit;
    }

    public function post($params = array()) {
        exit;
    }

    public function delete($params = array()) {
        exit;
    }
}
