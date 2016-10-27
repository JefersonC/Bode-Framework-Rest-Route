<?php

    namespace interfaces;

    interface restControllerInterface{
        
        public function get($params = array());
        public function post($params = array());
        public function put($params = array()); 
        public function delete($params = array()); 
        
    }
