<?php
include_once("ApiError.php");
class ApiRoute {
    private $uri = array();
    private $regexp_method = "(?'group'[a-zA-Z_]+)\.(?'name'[a-zA-Z_]+)(\?|$)";
    function __construct($prefix){
        $prefix = preg_quote($prefix, '/');
        if (preg_match("/".$prefix."./", $_SERVER["REQUEST_URI"]) === 1) {
            if (preg_match("/^".$prefix.$this->regexp_method."/", $_SERVER["REQUEST_URI"], $matches) === 1) {
                $this->uri["group"] = $matches["group"];
                $this->uri["name"] = $matches["name"];
            } else
                throw new ApiError(2);
        } else
            $this->ErrorPage403();
    }
    function getParsedUri(){
        if(!empty($this->uri))
            return $this->uri; 
    }
    function errorPage403(){
        header('HTTP/1.1 403 Forbidden');
        header("Status: 403 Forbidden");
        exit('<h1 align="center">403 Forbidden</h1><hr>'); 
    }
}
?>
