<?php
class Response {
    private $status;
    private $errors;
    private $data;
    
    public function __construct($status, $errors, $data){
        $this->status = $status;
        $this->errors = $errors;
        $this->data = $data;
    }
}
?>