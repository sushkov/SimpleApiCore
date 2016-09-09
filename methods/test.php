<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/lib/SimpleApiCore/ApiMethodsBase.php");
class test extends ApiMethodsBase {
    function __construct(){
        parent::$methods_list = array(["name" => "getTestList"],
                                      ["name" => "getTestStatus"],
                                      ["name" => "checkTestSignature", "s" => true],
                                      ["name" => "getRequest"],
                                      ["name" => "getSignature"]);
        parent::__construct();
    }

    function getTestList(){
        $list = array("one", "two", "three");
        return $this->responseResultList($list);
    }
    function getTestStatus(){
        return $this->responseStatus(rand(0,1));
    }
    function checkTestSignature(){
        return $this->responseStatus(1, ["message" => "signature is valid"]);
    }
    function getRequest($request){
        return $request;
    }
    function getSignature($request){
        $this->checkParameters($request, ["method", "secret"]);
        $method_name = $request["method"];
        $secret = $request["secret"];
        unset($request["method"]);
        unset($request["secret"]);
        $reqsize = count($request);
        $signhash = $method_name;
        if($reqsize > 0){
            $signhash .= "?";
            foreach($request as $key => $value)
                $signhash .= $key."=".$value."&";
            $signhash = substr($signhash, 0, strlen($signhash) - 1);
        }
        return array("string" => $signhash, "signature" => sha1($signhash.$secret));
    }
}