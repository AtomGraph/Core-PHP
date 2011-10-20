<?php

if(! defined('ROOTDIR')) {
    define('ROOTDIR', dirname(dirname(dirname(__FILE__))));
}

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

require_once ROOTDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Loader.php";

$loader = new \Graphity\Loader('Graphity', ROOTDIR . DS . "src" . DS . "main" . DS . "php");
$loader->register();

$loader = new \Graphity\Loader('PHPUnit', "/usr/local/zend/share"); 

require_once ROOTDIR . DS . "lib" . DS . "addendum" . DS . "lib" . DS . "annotations.php";

