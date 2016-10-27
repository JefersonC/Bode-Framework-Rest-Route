<?php

namespace models;

class customerModel extends Model {

     function __construct() {
        parent::__construct();
    }

    public function getCustomerByDomain($domain) {
        $this->setTable('customer');

        $s = array(
            'T0005_id'
        );

        $w = array(
            'T0005_domain = ?' => $domain
        );
        
        $rs = $this->select($s)->where($w)->run('FETCH');
        
         if($rs->rowCount === 1){
            return $rs->data->T0005_id;
        }
        return false;
    }

}
