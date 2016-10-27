<?php

namespace models;

class userModel extends Model {

    function __construct() {
        parent::__construct();
    }

    public function getUser($idCustomer, $credentials) {
        
        $this->setDatabase($idCustomer);
        
        
        $this->setTable('user');

        $s = array(
            'T0003_id'
        );

        $w = array(
            'T0003_login = ?' => $credentials['user'],
            'T0003_password = ?' => md5($credentials['pass'])
        );
        
        $rs = $this->select($s)->where($w)->run('FETCH');
        
        if($rs->rowCount === 1){
            return $rs->data->T0003_id;
        }
        return false;
    }
    
    public function updateUserToken($idUser) {
        $token = time();
        $u = array(
            'T0003_appToken' => $token
        );
        $w = array(
            'T0003_id = ?' => $idUser
        );
        
        $rs = $this->update($u)->where($w)->run();

        if($rs->rowCount === 1){
            return $token;
        }
        return false;
    }

}
