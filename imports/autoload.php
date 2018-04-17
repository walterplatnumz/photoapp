<?php

    if(!(defined("DS") && defined("SITE_ROOT") && defined("LIB_PATH"))) {
        define("DS", DIRECTORY_SEPARATOR);
        define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'].DS.'photoapp');      
        define('LIB_PATH', SITE_ROOT.DS.'imports');   
    }
    require_once(LIB_PATH.DS."configuration.php");
    require_once(LIB_PATH.DS."miscellaneous.php");
    require_once(LIB_PATH.DS."dbdml.php");
    require_once(LIB_PATH.DS."dboperation.php");
    require_once(LIB_PATH.DS."session.php");
    require_once(LIB_PATH.DS."photograph.php");

?>