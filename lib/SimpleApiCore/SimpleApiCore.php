<?php
include_once("ApiError.php");
include_once("ApiRoute.php");
include_once("ApiMethodsBase.php");
class SimpleApiCore{
    private $api_dir;
    private $methods_dir;
    private $methods_groups;
    private $client_secret;
    private $route_core;

    function __construct($c){
        $this->api_dir = $c[0];
        $this->methods_dir = (isset($c[1]) && is_string($c[1])) ? $c[1] : "methods";
        $this->methods_groups = (isset($c[2]) && is_array($c[2])) ? $c[2] : array();
        $this->client_secret = (isset($c[3]) && is_string($c[3])) ? $c[3] : null;
    }
    function __destruct(){
    }

    public function start(){
        try {
            $this->route_core = new ApiRoute();

            $method = $this->route_core->getParsedUri();
            $method_args = $_REQUEST;

            $api_name = strtolower($method["group"]);
            if (in_array($api_name, $this->methods_groups)){
                if (file_exists($this->api_dir . "/" . $this->methods_dir . "/" . $api_name . '.php')) {
                    $api = $this->getApi($api_name);
                    $api_method = $method["name"];
                    $method_settings = $api->checkMethod($api_method);
                    if ($method_settings) {
                        if ($method_settings["s"])
                            if (!$api->checkSignature($api_name . "." . $api_method, $method_args,
                                $this->client_secret))
                                throw new ApiError(6);

                        $result = $api->$api_method($method_args);
                        $this->responseResult($result);
                    } else {
                        throw new ApiError(2);
                    }
                } else {
                    throw new ApiError(1, "debug");
                }
            } else {
                throw new ApiError(2);
            }
        } catch(ApiError $e) {
            echo $e->responseError();
        }
    }

    public function setMethodsGroups($groups){
        if(isset($groups) && is_array($groups))
            $this->methods_groups = $groups;
    }
    private function getApi($group_name){
        include_once("ApiMethodsBase.php");
        include_once($this->api_dir."/" . $this->methods_dir . "/" . $group_name . ".php");
        return new $group_name();
    }
    private function responseResult($resp){
        echo json_encode(array("response" => $resp), JSON_UNESCAPED_UNICODE);
    }

}