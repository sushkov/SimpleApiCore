<?php
class ApiMethodsBase {
    protected static $methods_list;

    function __construct(){
        foreach(self::$methods_list as &$item)
            $item["name"] = strtolower($item["name"]);
    }
    public function checkSignature($method, $request, $secret){
        if(!isset($request["s"]))
            throw new ApiError(8);
        $signature = $request["s"];
        unset($request["s"]);
        $reqsize = count($request);
        $signhash = $method;
        if($reqsize > 0){
            $signhash .= "?"; 
            foreach($request as $key => $value)
                $signhash .= $key."=".$value."&";
            $signhash = substr($signhash, 0, strlen($signhash) - 1); 
        }
        $signhash = sha1($signhash.$secret);
        return ($signhash === $signature) ? true : false;
    }
    public function checkMethod($method_name){
        if($method_name != null)
            foreach(self::$methods_list as $method){
                if($method["name"] == strtolower($method_name)){
                    return $method;
                }
            }
        return false;
    }
    protected function responseResultList($items){
        return array("count" => count($items), "items" => $items);
    }
    protected function responseStatus($status, $add_response = null){
        $ret = array("success" => $status);
        if(isset($add_response) && !empty($add_response))
            foreach($add_response as $key => $value)
                $ret[$key] = $value;
        return $ret;
    }
    protected function checkParameters($request, $needed){
        if(empty($request))
            throw new ApiError(3);
        foreach($needed as $value)
            if(!isset($request[$value]))
                throw new ApiError(4, "common", $value);
    }
    function __destruct(){
    }
}