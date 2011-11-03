<?php

namespace Graphity\Tests;

use Graphity\Util as GU;

class UriBuilderExposed extends GU\UriBuilder {
    
    public $scheme = null;
    public $hostname = null;
    public $port = null;
    public $listOfSegments = array();
    public $listOfQueryParams = array();
    public $fragment = null;
}

class UriBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test___constructor() {
        $builder = new GU\UriBuilder();
        $this->assertTrue($builder instanceof GU\UriBuilder);
    }
    
    public function test_newInstance() {
        $builder = GU\UriBuilder::newInstance();
        $this->assertTrue($builder instanceof GU\UriBuilder);
    }
    
    public function fromUriProvider() {
        return array(
            array("http://example.org/", array(
                'scheme' => 'http',
                'hostname' => 'example.org',
            )),
            array("http://example.org/some/path",array(
                'scheme' => 'http',
                'hostname' => 'example.org',
                'listOfSegments' => array('some', 'path')
            )),
            array("http://example.org/?some=arg",array(
                'scheme' => 'http',
                'hostname' => 'example.org',
                'listOfQueryParams' => array('some' => 'arg')
            )),
            array("http://example.org/some/path?some=arg",array(
                'scheme' => 'http',
                'hostname' => 'example.org',
                'listOfSegments' => array('some', 'path'),
                'listOfQueryParams' => array('some' => 'arg')
            )),
            array("http://example.org/#fragment", array(
                'scheme' => 'http',
                'hostname' => 'example.org',
                'fragment' => 'fragment'
            )),
            array("http://example.org/some/path#fragment",array(
                'scheme' => 'http',
                'hostname' => 'example.org',
                'listOfSegments' => array('some', 'path'),
                'fragment' => 'fragment'
            )),
            array("http://example.org/?some=arg#fragment",array(
                'scheme' => 'http',
                'hostname' => 'example.org',
                'listOfQueryParams' => array('some' => 'arg'),
                'fragment' => 'fragment'
            )),
            array("http://example.org/some/path?some=arg#fragment",array(
                'scheme' => 'http',
                'hostname' => 'example.org',
                'listOfSegments' => array('some', 'path'),
                'listOfQueryParams' => array('some' => 'arg'),
                'fragment' => 'fragment'
            )),
            array("http://stage.example.org:8080/", array(
                'scheme' => 'http',
                'hostname' => 'stage.example.org',
                'port' => 8080,
            )),
        );
    }
    
    /**
     * @dataProvider fromUriProvider
     */
    public function test_fromUri($uri, $values) {
        $builder = UriBuilderExposed::fromUri($uri);
        
        $this->assertEquals($builder->scheme, array_key_exists('scheme', $values) ? $values['scheme'] : null);
        $this->assertEquals($builder->hostname, array_key_exists('hostname', $values) ? $values['hostname'] : null);
        $this->assertEquals($builder->port, array_key_exists('port', $values) ? $values['port'] : null);
        $this->assertEquals($builder->listOfSegments, array_key_exists('listOfSegments', $values) ? $values['listOfSegments'] : array());
        $this->assertEquals($builder->listOfQueryParams, array_key_exists('listOfQueryParams', $values) ? $values['listOfQueryParams'] : array());
        $this->assertEquals($builder->fragment, array_key_exists('fragment', $values) ? $values['fragment'] : null);
        
        $this->assertEquals($uri, $builder->build());
    }
    
    public function fromPathProvider() {
        return array(
            array("some/path", array(
                'listOfSegments' => array('some', 'path')
            )),
            array("/some/path",array(
                'listOfSegments' => array('', 'some', 'path')
            )),
            array("/some/path?foo=bar",array(
                'listOfSegments' => array('', 'some', 'path'),
                'listOfQueryParams' => array('foo' => 'bar')
            )),
            array("/some/path#fragment",array(
                'listOfSegments' => array('', 'some', 'path'),
                'fragment' => 'fragment'
            )),
            array("/some/path?foo=bar#fragment",array(
                'listOfSegments' => array('', 'some', 'path'),
                'listOfQueryParams' => array('foo' => 'bar'),
                'fragment' => 'fragment'
            )),
        );
    }
    
    /**
     * @dataProvider fromPathProvider
     */
    public function test_fromPath($path, $values) {
        $builder = UriBuilderExposed::fromPath($path);
        
        $this->assertEquals($builder->listOfSegments, array_key_exists('listOfSegments', $values) ? $values['listOfSegments'] : array());
        $this->assertEquals($builder->listOfQueryParams, array_key_exists('listOfQueryParams', $values) ? $values['listOfQueryParams'] : array());
        $this->assertEquals($builder->fragment, array_key_exists('fragment', $values) ? $values['fragment'] : null);
        
        $this->assertEquals($path, $builder->build());
    }
    
    public function buildFromMapProvider() {
        return array(
            array(
                array(
                    'path' => "something/good"
                ),
                array(),
                "something/good"
            ),
            array(
                array(
                    'path' => "something/wrong?foo=bar"
                ),
                array(),
                "something/wrong%3Ffoo%3Dbar"
            ),
            array(
                array(
                    'path' => "something/good",
                    'queryParam' => array("foo", "bar")
                ),
                array(),
                "something/good?foo=bar"
            ),
            array(
                array(
                    'path' => "something/good",
                    'fragment' => "?foo=bar"
                ),
                array(),
                "something/good#%3Ffoo%3Dbar"
            ),
            array(
                array(
                    'scheme' => 'http',
                    'host' => 'stage.example.org:8080',
                    'port' => 8080,
                ),
                array(),
                "http://stage.example.org:8080/",
            ),
            array(
                array(
                    'scheme' => 'http',
                    'host' => 'example.org',
                    'port' => 80,
                ),
                array(),
                'http://example.org/',
           ),
        );
    }
    
    /**
     * @dataProvider buildFromMapProvider
     */
    public function test_buildFromMap($stmts, $paramMap, $expected) {
        $builder = UriBuilderExposed::newInstance();
        
        foreach($stmts as $method => $value) {
            if(!is_array($value)) {
                call_user_func(array($builder, $method), $value);
            } else {
                call_user_func_array(array($builder, $method), $value);
            }
        }
        
        $this->assertEquals($expected, $builder->buildFromMap($paramMap));
    }
    
    public function buildProvider() {
        return array(
            array(
                array(
                    'path' => "something/good"
                ),
                array(),
                "something/good"
            ),
            array(
                array(
                    'path' => "something/wrong?foo=bar"
                ),
                array(),
                "something/wrong%3Ffoo%3Dbar"
            ),
            array(
                array(
                    'path' => "something/good",
                    'queryParam' => array("foo", "bar")
                ),
                array(),
                "something/good?foo=bar"
            ),
            array(
                array(
                    'path' => "something/good",
                    'fragment' => "?foo=bar"
                ),
                array(),
                "something/good#%3Ffoo%3Dbar"
            ),
            array(
                array(
                    'host' => 'example.com',
                    'path' => "/something/{param}",
                ),
                array("good"),
                "http://example.com/something/good"
            ),
        );
    }
    
    /**
     * @dataProvider buildProvider
     */
    public function test_build($stmts, $listOfParams, $expected) {
        $builder = UriBuilderExposed::newInstance();
        
        foreach($stmts as $method => $value) {
            if(!is_array($value)) {
                call_user_func(array($builder, $method), $value);
            } else {
                call_user_func_array(array($builder, $method), $value);
            }
        }
        
        $this->assertEquals($expected, call_user_func_array(array($builder, 'build'), $listOfParams));
    }
}
