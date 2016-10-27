<?php

namespace exceptions;

class restException extends \Exception {
    
    protected $messsage = "Unknown error";
    private $httpCode = 400;
    protected $code = 0;
    
    public function __construct($message = null, $httpCode = 400, $code = 0, Exception $previous = null) {
        if(null !== $message){
            $this->messsage = $message;            
        }
        
        $this->httpCode = $httpCode;
        $this->code = $code;
        
        parent::__construct($this->messsage, $this->code, $previous);
    }

    // personaliza a apresentação do objeto como string
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function httpOutput() {
        header('HTTP/1.1 ' . $this->httpCode . ' ' . $this->messsage);
        $rs = array(
            'status' => false,
            'message' => $this->messsage,
            'error' => $this->code
        );
        echo json_encode($rs, JSON_NUMERIC_CHECK);
        exit;
    }

}
