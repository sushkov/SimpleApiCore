<?php
include_once("/path_to_lib/SimpleApiCore/ApiMethodsBase.php");
class my_methods_group extends ApiMethodsBase {
    function __construct(){
        parent::$methods_list = array(["name" => "my_method"/*, "s" => true*/] /* my other methods */);
        parent::__construct();
    }

    function my_method(/* $request */){
        /*
         * code of my method here..
         */
    }
}