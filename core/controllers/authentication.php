<?php

/*
 * erros
 * 200: dominio não existe
 * 201: usuário não existe
*/

namespace controllers;

class authentication extends controller implements \interfaces\restControllerInterface {
    private $customer;
    private $user;
    
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

    public function logar($params = array()) {
        $rs = array(
            'status' => false,
            'message' => '',
            'error' => false,
            'data' => array()
        );

        try {

            $this->checkCredentials($params['input']);

            $companyId = $this->customer->getCustomerByDomain($params['input']['domain']);
      
            if($companyId == false){
                throw new \Exception('Domain not exists.', 200);
            }
            
            $user = $this->user->getUser($companyId, $params['input']);
            
            if($user == false){
                throw new \Exception('User not exists', 201);
            }
            
            $token = $this->user->updateUserToken($user);
            
            if($token == false){
                throw new \Exception('Error when generate token', 201);
            }
            
            $token = $companyId . '-' . $token;
            
            $rs['status'] = true;
            $rs['data'] = array(
                'token' => $token
            );
            
        } catch (\Exception $e) {
            $rs['message'] = $e->getMessage();
            $rs['error'] = $e->getCode();
        }
        
        toJson($rs);
    }

    private function checkCredentials($param) {
        
        if (\filters\filter::required($param['domain']) === false) {
            throw new \Exception("Domain is required", 106);
        }
        
        if (\filters\filter::email($param['user']) === false) {
            throw new \Exception("Email is required", 106);
        }
        
        if (\filters\filter::required($param['pass']) === false) {
            throw new \Exception("Password is required", 106);
        }
    }

}
