<?php

namespace Graphity\Tests;

use Graphity\Cookie;
use Graphity\Response;
use Graphity\ResponseBuilder;

class ResponseExposed extends Response
{
    public $cookies = array();
}

class ResponseBuilderExposed extends ResponseBuilder
{
    public $response = null;

    public function __construct() {
        $this->response = new ResponseExposed();
    }
}

class ResponseBuilderTest extends \PHPUnit_Framework_TestCase
{
    const CACHE_CONTROL = "max-age=600, s-maxage=1800, must-revalidate";
    const CONTENT_LOCATION = "http://example.org/new";
    const LAST_MODIFIED = "Mon, 24 Oct 2011 22:10:52 GMT";
    const VIA = "Some-Proxy";
    const LANGUAGE = "lt_LT";
    const LOCATION = "http://example.org/new";
    const CONTENT_TYPE = "text/plain";
    const VARIANT = "Accept";

    const COOKIE_NAME = "UserId";
    const COOKIE_VALUE = "12345";

    const ENTITY = "<p>I'm okay.</p>";
    const STATUS = 200;

    public function test_newInstance() {
        $builder = ResponseBuilder::newInstance();
        $this->assertTrue($builder instanceof ResponseBuilder);
        $this->assertNotNull($builder->build());
    }

    public function specificHeadersProvider() {
        return array(
            /** methodName, value, arrayKey */
            array("cacheControl", self::CACHE_CONTROL, "Cache-Control"),
            array("contentLocation", self::CONTENT_LOCATION, "Content-Location"),
            array("language", self::LANGUAGE, "Content-Language"),
            array("lastModified", self::LAST_MODIFIED, "Last-Modified"),
            array("location", self::LOCATION, "Location"),
            array("variant", self::VARIANT, "Vary"),
        );
    }

    /**
     *  @dataProvider specificHeadersProvider
     */
    public function test_specificHeaders($methodName, $value, $arrayKey) {
        $response = ResponseBuilder::newInstance()->$methodName($value)->build();
        $headers = $response->getHeaders();
        $this->assertArrayHasKey($arrayKey, $response->getHeaders());
        $this->assertEquals($value, $headers[$arrayKey]);
    }

    public function test_cookie() {
        $cookie = new Cookie(self::COOKIE_NAME, self::COOKIE_VALUE);
        // suppress warning about headers already sent.
        $old = error_reporting(E_ALL & ~E_WARNING);
        $response = ResponseBuilderExposed::newInstance()->cookie($cookie)->build();
        error_reporting($old);
        $listOfCookies = $response->getCookies();
        $this->assertEquals(1, count($listOfCookies));
        $cookie = $listOfCookies[0];
        $this->assertEquals($cookie->getName(), self::COOKIE_NAME);
        $this->assertEquals($cookie->getValue(), self::COOKIE_VALUE);
    }

    public function test_entity() {
        $response = ResponseBuilderExposed::newInstance()->entity(self::ENTITY)->build();
        $this->assertEquals($response->getBuffer(), self::ENTITY);
    }

    public function test_status() {
        $response = ResponseBuilderExposed::newInstance()->status(self::STATUS)->build();
        $this->assertEquals($response->getStatus(), self::STATUS);
    }
}

