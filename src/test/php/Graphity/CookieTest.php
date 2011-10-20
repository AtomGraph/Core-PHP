<?php

namespace Graphity\Tests;

use Graphity;

class CookieTest extends \PHPUnit_Framework_TestCase
{

    const NAME = "cookiename";

    const VALUE = "cookievalue";

    public function test___construct()
    {
        $cookie = new Graphity\Cookie(static::NAME, static::VALUE);
        
        $this->assertEquals(static::NAME, $cookie->getName());
        $this->assertEquals(static::VALUE, $cookie->getValue());
    }

    public function getSettersTests()
    {
        return array(array("Path", "/strip"), array("Value", static::VALUE), array("MaxAge", date("Y-m-d H:i:s")));
    }

    /**
     * @dataProvider getSettersTests
     */
    public function test_setters($property, $value)
    {
        $class = new Graphity\Cookie(static::NAME, static::VALUE);
        
        $setter = "set" . $property;
        $getter = "get" . $property;
        
        $class->$setter($value);
        $this->assertEquals($value, $class->$getter());
    }
}
