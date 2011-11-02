<?php

namespace Graphity\Tests;

use Graphity;

class ResourceExposed extends Graphity\Resource
{
    public function exists()
    {
        return true;
    }

    public function describe()
    {
        return "";
    }
}

class ResourceTest extends \PHPUnit_Framework_TestCase
{

    const HTTP_HOST = "http://localhost";

    public function getURIProvider()
    {
        return array(array("/", "/"), array("/strip/", "/strip"), array("/strip", "/strip"), array("/strip/2011/", "/strip/2011"), array("/strip/2011", "/strip/2011"));
    }

    /**
     * @dataProvider getURIProvider
     */
    public function test_getURI($realPath, $expectedPath)
    {
        $router = new Graphity\Router(include(dirname(__FILE__) . "/routes.php"));

        $request = $this->getMock('Graphity\\Request', array('getScheme', 'getServerName', 'getPathInfo'));
        $request->expects($this->any())
                ->method('getScheme')
                ->will($this->returnValue("http"));
        $request->expects($this->any())
                ->method('getPathInfo')
                ->will($this->returnValue($realPath));
        $request->expects($this->any())
                ->method('getServerName')
                ->will($this->returnValue("localhost"));

        $resource = new ResourceExposed($request, $router);
        $this->assertEquals(static::HTTP_HOST . $expectedPath, $resource->getURI());
    }

    public function getPathProvider()
    {
        return array(
            /* pathInfo, expectedPath */
            array("/", ""),
            array("/path/", "/path"),
            array("/path", "/path"),
            array("/sub/path/", "/sub/path"),
            array("/sub/path", "/sub/path"),
        );
    }

    /**
     * @dataProvider getPathProvider
     */
    public function test_getPath($pathInfo, $expected) {
        $router = new Graphity\Router(include(dirname(__FILE__) . "/routes.php"));

        $request = $this->getMock('Graphity\\Request', array('getPathInfo'));
        $request->expects($this->any())
                ->method('getPathInfo')
                ->will($this->returnValue($pathInfo));

        $resource = new ResourceExposed($request, $router);
        $this->assertEquals($expected, $resource->getPath());
    }
}

