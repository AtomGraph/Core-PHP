<?php

namespace Graphity\Tests;

use Graphity;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function getRequestURIProvider()
    {
        return array(
            /** RequestUri, ServerName, ServerPort, PathInfo, Expected */
            array("http://localhost/", null, null, null, "http://localhost/"),
            array(null, "localhost", null, "/", "http://localhost/"),
            array(null, "localhost", 80, "/", "http://localhost/"),
            array(null, "localhost", 80, "/path", "http://localhost/path"),
            array(null, "localhost", 8080, "/", "http://localhost:8080/"),
            array(null, "localhost", 8080, "/path", "http://localhost:8080/path"), 
        );
    }

    /**
     *  @dataProvider getRequestURIProvider
     */
    public function test_getRequestURI($requestUri, $serverName, $serverPort, $pathInfo, $expected)
    {
        $request = $this->getMock('Graphity\\Request', array('getHeader', 'getServerPort', 'getPathInfo'));
        if($requestUri !== null) {
            $request->expects($this->any())
                    ->method('getHeader')
                    ->will($this->onConsecutiveCalls($requestUri, $requestUri));
        } else {
            $request->expects($this->any())
                    ->method('getHeader')
                    ->will($this->onConsecutiveCalls($requestUri, $serverName));
        }
        $request->expects($this->any())
                ->method('getServerPort')
                ->will($this->returnValue($serverPort));
        $request->expects($this->any())
                ->method('getPathInfo')
                ->will($this->returnValue($pathInfo));

        $this->assertEquals($expected, $request->getRequestURI());
    }
}
