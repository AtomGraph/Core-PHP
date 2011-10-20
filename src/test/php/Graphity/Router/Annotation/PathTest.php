<?php

namespace Graphity\Tests\Router\Annotation;

use Graphity;

if(!defined('GRAPHITYDIR')) {
    define('GRAPHITYDIR', ROOTDIR . DS . "src" . DS . "main" . DS . "php");
}

include_once GRAPHITYDIR . DS . "Graphity" . DS . "Router" . DS . "Annotation" . DS . "Path.php";

class PathTest extends \PHPUnit_Framework_TestCase
{

    public function buildPathProvider()
    {
        return array(array("/strip/{year}", "/strip/{year}"), array("/strip/{year}/", "/strip/{year}"), array("/strip/{year: \\d{4}}", "/strip/{year}"), array("/strip/{year: \\d{4}}/", "/strip/{year}"), array("/strip/{year: \\d{4}}/{month}", "/strip/{year}/{month}"), array("/strip/{year: \\d{4}}/{month}/", "/strip/{year}/{month}"), array("/strip/{year: \\d{4}}/{month: \\d{2}}", "/strip/{year}/{month}"), array("/strip/{year: \\d{4}}/{month: \\d{2}}/", "/strip/{year}/{month}"));
    }

    /**
     * @dataProvider buildPathProvider
     */
    public function test_getBuildPath($annotation, $buildPath)
    {
        $target = $this->getMockBuilder("ReflectionAnnotatedClass")->disableOriginalConstructor()->getMock();
        
        $path = new /*Graphity\Router\Annotation*/\Path(array("value" => $annotation), $target);
        $this->assertEquals($buildPath, $path->getBuildPath());
        unset($path);
    }

    public function matchPathProvider()
    {
        return array(array("/strip/{year}", "^\/strip\/(?<year>[^\/]+)$", "/strip/2011"), array("/strip/{year}/", "^\/strip\/(?<year>[^\/]+)$", "/strip/2011"), array("/strip/{year: \\d{4}}", "^\/strip\/(?<year>\\d{4})$", "/strip/2011"), array("/strip/{year: \\d{4}}/", "^\/strip\/(?<year>\\d{4})$", "/strip/2011"), array("/strip/{year: \\d{4}}/{month}", "^\/strip\/(?<year>\\d{4})\/(?<month>[^\/]+)$", "/strip/2011/06"), array("/strip/{year: \\d{4}}/{month}/", "^\/strip\/(?<year>\\d{4})\/(?<month>[^\/]+)$", "/strip/2011/06"), array("/strip/{year: \\d{4}}/{month: \\d{2}}", "^\/strip\/(?<year>\\d{4})\/(?<month>\\d{2})$", "/strip/2011/06"), array("/strip/{year: \\d{4}}/{month: \\d{2}}/", "^\/strip\/(?<year>\\d{4})\/(?<month>\\d{2})$", "/strip/2011/06"));
    }

    /**
     * @dataProvider matchPathProvider
     */
    public function test_getMatchPath($annotation, $matchPath, $testUri)
    {
        $target = $this->getMockBuilder("ReflectionAnnotatedClass")->disableOriginalConstructor()->getMock();
        
        $path = new /*Graphity\Router\Annotation*/\Path(array("value" => $annotation), $target);
        $this->assertEquals($matchPath, $path->getMatchPath());
        $this->assertTrue(preg_match("/" . $path->getMatchPath() . "/", $testUri) === 1);
        unset($path);
    }
}
