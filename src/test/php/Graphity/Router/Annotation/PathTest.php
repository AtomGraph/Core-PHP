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
        return array(
            array("/strip/{year}", "/strip/{year}"), 
            array("/strip/{year}/", "/strip/{year}"), 
            array("/strip/{year: \\d{4}}", "/strip/{year}"), 
            array("/strip/{year: \\d{4}}/", "/strip/{year}"), 
            array("/strip/{year: \\d{4}}/{month}", "/strip/{year}/{month}"), 
            array("/strip/{year: \\d{4}}/{month}/", "/strip/{year}/{month}"), 
            array("/strip/{year: \\d{4}}/{month: \\d{2}}", "/strip/{year}/{month}"), 
            array("/strip/{year: \\d{4}}/{month: \\d{2}}/", "/strip/{year}/{month}")
        );
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
        return array(
            array("/strip/{year}", "^\/strip\/(?<year>[^\/]+)(\\/.*)?", "/strip/2011"), 
            array("/strip/{year}/", "^\/strip\/(?<year>[^\/]+)(\\/.*)?", "/strip/2011"), 
            array("/strip/{year: \\d{4}}", "^\/strip\/(?<year>\\d{4})(\\/.*)?", "/strip/2011"), 
            array("/strip/{year: \\d{4}}/", "^\/strip\/(?<year>\\d{4})(\\/.*)?", "/strip/2011"), 
            array("/strip/{year: \\d{4}}/{month}", "^\/strip\/(?<year>\\d{4})\/(?<month>[^\/]+)(\\/.*)?", "/strip/2011/06"), 
            array("/strip/{year: \\d{4}}/{month}/", "^\/strip\/(?<year>\\d{4})\/(?<month>[^\/]+)(\\/.*)?", "/strip/2011/06"), 
            array("/strip/{year: \\d{4}}/{month: \\d{2}}", "^\/strip\/(?<year>\\d{4})\/(?<month>\\d{2})(\\/.*)?", "/strip/2011/06"), 
            array("/strip/{year: \\d{4}}/{month: \\d{2}}/", "^\/strip\/(?<year>\\d{4})\/(?<month>\\d{2})(\\/.*)?", "/strip/2011/06")
        );
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

    public function characterCountProvider()
    {
        return array(
            array("/strip/{year}", 5), 
            array("/strip/{year}/", 5), 
            array("/strip/{year: \\d{4}}", 5), 
            array("/strip/{year: \\d{4}}/", 5), 
            array("/strip/{year: \\d{4}}/{month}", 5), 
            array("/strip/{year: \\d{4}}/{month}/", 5), 
            array("/strip/{year: \\d{4}}/{month: \\d{2}}", 5), 
            array("/strip/{year: \\d{4}}/{month: \\d{2}}/", 5),
            array("/admin/strip/{year}", 10), 
            array("/admin/strip/{year}/", 10), 
            array("/admin/{type}/settings", 13), 
            array("/admin/{type}/settings/", 13)
        );
    }

    /**
     * @dataProvider characterCountProvider
     */
    public function test_getLiteralCharacterCount($buildPath, $expectedCount)
    {
        $target = $this->getMockBuilder("ReflectionAnnotatedClass")->disableOriginalConstructor()->getMock();

        $path = $this->getMockBuilder("\\Path")
                     ->disableOriginalConstructor()
                     ->setMethods(array("getBuildPath"))
                     ->getMock();
        $path->expects($this->any())
             ->method("getBuildPath")
             ->will($this->returnValue($buildPath));

        $this->assertEquals($path->getLiteralCharacterCount(), $expectedCount);
    }

    public function parameterCountProvider()
    {
        return array(
            array("/strip/{year}", 1, 0), 
            array("/strip/{year}/", 1, 0), 
            array("/strip/{year: \\d{4}}", 1, 1), 
            array("/strip/{year: \\d{4}}/", 1, 1), 
            array("/strip/{year: \\d{4}}/{month}", 2, 1), 
            array("/strip/{year: \\d{4}}/{month}/", 2, 1), 
            array("/strip/{year: \\d{4}}/{month: \\d{2}}", 2, 2), 
            array("/strip/{year: \\d{4}}/{month: \\d{2}}/", 2, 2),
            array("/admin/strip/{year}", 1, 0), 
            array("/admin/strip/{year}/", 1, 0), 
            array("/admin/{type}/settings", 1, 0), 
            array("/admin/{type}/settings/", 1, 0)
        );
    }

    /**
     * @dataProvider parameterCountProvider
     */
    public function test_getParameterCount($value, $includingDefault, $excludingDefault) {
        $target = $this->getMockBuilder("ReflectionAnnotatedClass")->disableOriginalConstructor()->getMock();

        $path = new \Path(array("value" => $value), $target);
        $this->assertEquals($path->getParameterCount(), $includingDefault);
        $this->assertEquals($path->getParameterCount(true), $includingDefault);
        $this->assertEquals($path->getParameterCount(false), $excludingDefault);
    }

    public function compareProvider()
    {
        return array(
            array(array(1, 0), array(), array(), -1),
            array(array(0, 1), array(), array(), 1),
            array(array(1, 1), array(1, 0), array(), -1),
            array(array(1, 1), array(0, 1), array(), 1),
            array(array(1, 1), array(1, 1), array(1, 0), -1),
            array(array(1, 1), array(1, 1), array(0, 1), 1),
            array(array(1, 1), array(1, 1), array(1, 1), 0),
        );
    }

    /**
     * @dataProvider compareProvider
     */
    public function test_compare(array $characterCount, array $paramCount, array $withoutDefault, $expectedResult)
    {
        $pathA = $this->getMockBuilder("\\Path")
                     ->disableOriginalConstructor()
                     ->setMethods(array("getLiteralCharacterCount",
                                        "getParameterCount"))
                     ->getMock();

        $pathA->expects($this->once())
              ->method("getLiteralCharacterCount")
              ->will($this->returnValue($characterCount[0]));

        if(!empty($withoutDefault)) {
            $pathA->expects($this->exactly(2))
                  ->method("getParameterCount")
                  ->will($this->onConsecutiveCalls($paramCount[0], $withoutDefault[0]));
        } elseif(!empty($paramCount)) {
            $pathA->expects($this->once())
                 ->method("getParameterCount")
                 ->will($this->returnValue($paramCount[0]));
        } else {
            $pathA->expects($this->exactly(0))
                  ->method("getParameterCount")
                  ->will($this->returnValue("deadbeef"));
        }
                

        $pathB = $this->getMockBuilder("\\Path")
                     ->disableOriginalConstructor()
                     ->setMethods(array("getLiteralCharacterCount",
                                        "getParameterCount"))
                     ->getMock();

        $pathB->expects($this->once())
              ->method("getLiteralCharacterCount")
              ->will($this->returnValue($characterCount[1]));

        if(!empty($withoutDefault)) {
            $pathB->expects($this->exactly(2))
                  ->method("getParameterCount")
                  ->will($this->onConsecutiveCalls($paramCount[1], $withoutDefault[1]));
        } elseif(!empty($paramCount)) {
            $pathB->expects($this->once())
                 ->method("getParameterCount")
                 ->will($this->returnValue($paramCount[1]));
        } else {
            $pathB->expects($this->exactly(0))
                  ->method("getParameterCount")
                  ->will($this->returnValue("deadbeef"));
        }

        $this->assertEquals($pathA->compare($pathB), $expectedResult);
    }
}
