<?php

namespace Graphity\Tests;

use Graphity;

class ResourceExposed extends Graphity\Resource
{
    public function exists()
    {
        return true;
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

        $request = $this->getMock('Graphity\\Request', array('getRequestURI', 'getServerName'));
        $request->expects($this->any())
                ->method('getRequestURI')
                ->will($this->returnValue($realPath));
        $request->expects($this->any())
                ->method('getServerName')
                ->will($this->returnValue("localhost"));

        $resource = new ResourceExposed($request, $router);
        $this->assertEquals(static::HTTP_HOST . $expectedPath, $resource->getURI());
    }

    public function settersProvider()
    {
        return array(
            array("URI", "http://localhost/")
        );
    }

    /**
     * @dataProvider settersProvider
     */
    public function test_setters($property, $value)
    {
        $router = new Graphity\Router(include(dirname(__FILE__) . "/routes.php"));

        $request = $this->getMock('Graphity\Request', array('getRequestURI', 'getServerName'));
        $request->expects($this->any())
                ->method('getRequestURI')
                ->will($this->returnValue("/test"));
        $request->expects($this->any())
                ->method('getServerName')
                ->will($this->returnValue("localhost"));

        $resource = new ResourceExposed($request, $router);

        $setter = "set" . $property;
        $getter = "get" . $property;
        
        $resource->$setter($value);
        $this->assertEquals($value, $resource->$getter());
    }
}

