<?php
include_once("lib/SimpleApiCore/SimpleApiCore.php");
    $api_config = array(
        $_SERVER["DOCUMENT_ROOT"], //api workspace
        "methods", //name of methods directory (default - methods)
        array("test"), //names of method groups
        "test_secret", //secret word for signature
        "/apishechka/" //prefix to method url (default - '')
    );
    $api_core = new SimpleApiCore($api_config);
    $api_core->start();