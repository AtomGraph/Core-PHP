<?php

if(!defined('ROOTDIR')) {
    define('ROOTDIR', dirname(dirname(__FILE__)));
}
if(!defined('GRAPHITYDIR')) {
    define('GRAPHITYDIR', dirname(dirname(__FILE__)));
}
if(!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if(!defined('PS')) {
    define('PS', PATH_SEPARATOR);
}
define('TAB', "    ");

require_once GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Loader.php";
require_once GRAPHITYDIR . DS . "lib" . DS . "addendum" . DS . "lib" . DS . "annotations.php";

$loader = new \Graphity\Loader('Graphity', GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php");
$loader->register();

require_once GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Router" . DS . "Annotation" . DS . "Path.php";
require_once GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Router" . DS . "Annotation" . DS . "Singleton.php";
require_once GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Router" . DS . "Annotation" . DS . "GET.php";
require_once GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Router" . DS . "Annotation" . DS . "POST.php";
require_once GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Router" . DS . "Annotation" . DS . "DELETE.php";
require_once GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Router" . DS . "Annotation" . DS . "PUT.php";
require_once GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Router" . DS . "Annotation" . DS . "Produces.php";
require_once GRAPHITYDIR . DS . "src" . DS . "main" . DS . "php" . DS . "Graphity" . DS . "Router" . DS . "Annotation" . DS . "Consumes.php";

if(count($argv) < 3) {
    echo "Usage: " . basename($argv[0]) . " <resource-directory> <output-file-path>\n";
    exit();
}

$dirPath = $argv[1];
$outPath = $argv[2];

if(is_dir($outPath)) {
    echo "Output path should be a file, not directory.\n";
    exit();
}

if(! is_writable($outPath)) {
    echo "Output path should be writable.\n";
    exit();
}

$scanner = new \Graphity\Util\Scanning\FilesScanner($dirPath, true);
$listener = new \Graphity\Router\Scanner\RouteAnnotationListener();
$scanner->scan($listener);

$routesStr = "<" . "?php\n";
$routesStr .= "/**\n" . " *	This file was automaticaly generated using command line: \n" . " *	" . basename($argv[0]) . " " . $dirPath . " " . $outPath . "\n" . " * \n" . " * 	Use the same command to update it.\n" . " */\n\n";
$routesStr .= "return array(\n";

$listOfAnnotations = $listener->getAnnotations();
usort($listOfAnnotations, function($a, $b) {
    // count = segmentCount + (segmentCount - parameterCount)
    $aCount = 2 * $a['path']->getSegmentCount() - $a['path']->getParameterCount();
    $bCount = 2 * $b['path']->getSegmentCount() - $b['path']->getParameterCount();
    
    if($aCount > $bCount) {
        return -1;
    } elseif($aCount < $bCount) {
        return 1;
    }
    
    return 0;
});

foreach($listOfAnnotations as $annotation) {
    $className = $annotation['className'];
    $matchPath = $annotation['path']->getMatchPath();
    $route = array(
        'buildPath' => $annotation['path']->getBuildPath(),
        'matchPath' => "/" . $matchPath . "/",
    );
    usort($annotation['items'], function($a, $b) {
        $aCount = $bCount = 0;
        $weights = array(
            "GET" => 10,
            "POST" => 7,
            "DELETE" => 4,
            "PUT" => 1,
        );

        $aCount = $weights[strtoupper($a['requestMethod'])] + (@count($a['consumes'])) + (@count($a['produces']));
        $bCount = $weights[strtoupper($b['requestMethod'])] + (@count($b['consumes'])) + (@count($b['produces']));

        if($aCount > $bCount) {
            return -1;
        } elseif($aCount < $bCount) {
            return 1;
        }

        return 0;
    });
    foreach($annotation['items'] as $method) {
        $requestMethod = $method['requestMethod'];
        $classMethod = $method['classMethod'];
        $consumes = "null";
        if(array_key_exists('consumes', $method)) {
            $consumes = var_export($method['consumes'], true);
        }
        $produces = "null";
        if(array_key_exists('produces', $method)) {
            $produces = var_export($method['produces'], true);
        }

        if(!array_key_exists($requestMethod, $route)) {
            $route[$requestMethod] = array();
        }
        $route[$requestMethod][] = array(
           'methodName' => $classMethod,
           'consumes' => array_key_exists('consumes', $method) ? $method['consumes'] : null,
           'produces' => array_key_exists('produces', $method) ? $method['produces'] : null,
        );
    }
    $routesStr .= "'{$className}' => " . var_export($route, true) . ",\n";
}

$routesStr .= ");\n";

file_put_contents($outPath, $routesStr);
chmod($outPath, 0777);
