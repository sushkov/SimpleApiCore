<?php
class ApiError extends Exception{
    public static $errors_list = array("common" => array(1 => "Access denied",
                                                         2 => "Method does not exist",
                                                         3 => "Required parameters of method",
                                                         4 => "Required parameter",
                                                         5 => "Incorrect parameter",
                                                         6 => "Incorrect signature",
                                                         7 => "Internal server error",
                                                         8 => "Required signature"),
                                        "debug" => array(1 => "File with required api does not exist"),
                                        "user" => array());

    private $status;
    function __construct($code, $stat = "common", $add_msg = ""){
        if(array_key_exists($stat, self::$errors_list)){
            $this->code = $code;
            if($stat === "user"){
                $this->message = $add_msg;
            } else {
                if(array_key_exists($code, self::$errors_list[$stat])){
                    $this->message = self::$errors_list[$stat][$code];
                    if($add_msg != "")
                        $this->message .= ($stat === "debug") ? " ( $add_msg )" : " $add_msg";
                    $this->status = ($stat != "common") ? $stat : null;
                }
            }
        }
    }    
    function responseError(){
        $error = array();
        if($this->status)
            $error["status"] = $this->status;
        $error["code"] = $this->code; 
        $error["description"] = $this->message;
        return json_encode(array("error" => $error));     
    }
}