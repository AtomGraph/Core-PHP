<?php

namespace Graphity\Tests;

require_once dirname(__FILE__) . '/SampleResources.php';

use Graphity;

class RouterExposed extends Graphity\Router
{
    public $routes = array();
}

class RouterTest extends \PHPUnit_Framework_TestCase
{

    protected static $routingTable;

    public function setUp() {
        self::$routingTable = include(dirname(__FILE__) . "/routes.php");
    }

    public function tearDown() {
        self::$routingTable = array();
    }

    public function test___constructor()
    {
        $router = new RouterExposed(self::$routingTable);
        
        $this->assertArrayHasKey('FrontPageResource', $router->routes);
        $this->assertArrayHasKey('buildPath', $router->routes['FrontPageResource']);
        $this->assertArrayHasKey('matchPath', $router->routes['FrontPageResource']);
        $this->assertEquals("/", $router->routes['FrontPageResource']['buildPath']);
        $this->assertEquals("/^\/$/", $router->routes['FrontPageResource']['matchPath']);
    }

    public function matchPathProvider() {
        return array(
            array(null, "/invalid/path"),
            array("PostListResource", "/posts"),
            array(null, "/post/12345"),
            array(null, "/post/2011"),
            array(null, "/post/1234/bb"),
            array(null, "/post/2011/06"),
            array(null, "/post/1234/56/999"),
            array("PostResource", "/post/2011/06/16"),
            array("FrontPageResource", "/"),
        );
    }

    /**
     *  @dataProvider matchPathProvider
     */
    public function test_matchPath($expected, $path)
    {
        $router = new RouterExposed(self::$routingTable);
        $this->assertEquals($expected, $router->matchPath($path));
    }

    public function matchURIProvider() {
        $listOfPaths = $this->matchPathProvider();
        $listOfURIs = array();

        foreach($listOfPaths as $path) {
            $listOfURIs[] = array($path[0], "http://localhost" . $path[1]);
        }
        return $listOfURIs;
    }

    /**
     *  @dataProvider matchURIProvider
     */
    public function test_matchURI($expected, $uri)
    {
        $router = new RouterExposed(self::$routingTable);
        $this->assertEquals($expected, $router->matchURI($uri));
    }

    /**
     *  @dataProvider matchURIProvider
     */
    public function test_matchRequest($expected, $uri) {
        $request = $this->getMock('Graphity\\Request', array('getRequestURI'));
        $request->expects($this->any())
                ->method('getRequestURI')
                ->will($this->returnValue($uri));

        $router = new RouterExposed(self::$routingTable);
        $this->assertEquals($expected, $router->matchRequest($request));
    }

    public function matchResourceProvider() {
        return array(
            array("PostListResource", "http://localhost/posts"),
            array("PostResource", "http://localhost/post/2011/06/16"),
            array("FrontPageResource", "http://localhost/"),
        );
    }

    /**
     *  @dataProvider matchResourceProvider
     */
    public function test_matchResource($className, $uri) {
        $request = $this->getMock('Graphity\\Request', array('getRequestURI'));
        $request->expects($this->any())
                ->method('getRequestURI')
                ->will($this->returnValue($uri));

        $router = new RouterExposed(self::$routingTable);
        $result = $router->matchResource($request);
        if($className === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($className, get_class($result));
        }
    }

    public function matchMethodProvider() {
        return array(
            /** Resource, Method, ContentType, Accept, Expected method */
            array("FrontPageResource", "GET", null, "text/html,application/xml", "doGet"),
            array("FrontPageResource", "POST", "application/x-www-form-urlencoded", "*/*", "doPost"),
            array("FrontPageResource", "POST", "multipart/form-data", "application/xml+rdf", null),
            array("FrontPageResource", "POST", "multipart/form-data", "*/*", null),
            array("PostResource", "GET", null, "application/json", null),
            array("PostResource", "GET", null, "text/html", "doGet"),
            array("PostResource", "GET", null, "application/rdf+xml", "doGet"),
            array("PostResource", "POST", "multipart/form-data", "application/xml", null),
            array("PostResource", "POST", "multipart/form-data", "application/json", null),
            array("PostResource", "POST", "multipart/form-data", "text/html", null),
            array("PostResource", "POST", "application/x-www-form-urlencoded", "application/json", "doPost"),
            array("PostResource", "POST", "application/x-www-form-urlencoded", "text/html", "doPost"),
            array("PostResource", "POST", "application/x-www-form-urlencoded", "application/xml", null),
            array("AdminFrontPageResource", "GET", null, "application/rdf+xml", "rdf"),
            array("AdminFrontPageResource", "GET", null, "text/html", "doGet"),
            array("AdminFrontPageResource", "GET", null, "*/*", "rdf"),
            array("AdminFrontPageResource", "DELETE", null, "text/html", "doDelete"),
            array("AdminFrontPageResource", "POST", "multipart/form-data", "application/xml", "doPost"),
            array("AdminFrontPageResource", "POST", "multipart/form-data", "application/json", "doPost"),
            array("AdminFrontPageResource", "POST", "multipart/form-data", "text/html", "doPost"),
            array("AdminFrontPageResource", "POST", "application/x-www-form-urlencoded", "application/json", "doPost"),
            array("AdminFrontPageResource", "POST", "application/x-www-form-urlencoded", "text/html", "saveModel"),

            array("AdminFrontPageResource", "POST", "multipart/form-data-alternate", "application/json", "doPost"),
            array("AdminFrontPageResource", "POST", "multipart/form-data; boundary=---WebKit1234", "application/json", "doPost"),
            array("AdminFrontPageResource", "POST", "multipart/form-data; boundary=---WebKit1234", "application/json", "doPost"), 

            /* This is accept headers from the infamous IE. I hate them!!! 
             * (Notice how text/html or anything else is missing, WTH?!) */
            array("FrontPageResource", "GET", null, "image/gif, image/jpeg, image/pjpeg, application/x-ms-application, application/vnd.ms-xpsdocument, application/xaml+xml, application/x-ms-xbap, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, application/x-shockwave-flash, */*", "doGet"),
        );
    }

    /**
     *  @dataProvider matchMethodProvider
     */
    public function test_matchMethod($className, $requestMethod, $contentType, $accept, $expected) {
        $session = $this->getMockBuilder('Graphity\\Session')
                        ->disableOriginalConstructor()
                        ->setMethods(array('getAttribute'))
                        ->getMock();
        $session->expects($this->any())
                ->method('getAttribute')
                ->will($this->returnValue(false));

        $request = $this->getMock('Graphity\\Request', array('getMethod', 'getContentType', 'getHeader', 'getSession'));
        $request->expects($this->any())
                ->method('getMethod')
                ->will($this->returnValue($requestMethod));
        $request->expects($this->any())
                ->method('getContentType')
                ->will($this->returnValue($contentType));
        $request->expects($this->any())
                ->method('getHeader')
                ->will($this->returnValue($accept));
        $request->expects($this->any())
                ->method('getSession')
                ->will($this->returnValue($session));

        $router = new RouterExposed(self::$routingTable);

        $resource = new $className($request, $router);

        $this->assertEquals($expected, $router->matchMethod($resource));
    }

    public function buildURIProvider()
    {
        return array(array("FrontPageResource", array("key" => "value"), "/"), array("PostListResource", array(), "/posts"), array("PostResource", array("year" => "2011", "month" => "06", "day" => "16"), "/post/2011/06/16"));
    }

    /**
     * @dataProvider buildURIProvider
     */
    public function test_buildURI($resource, $params, $uri)
    {
        $router = new RouterExposed(static::$routingTable);
        
        $this->assertEquals($uri, $router->buildURI($resource, $params));
    }
}

