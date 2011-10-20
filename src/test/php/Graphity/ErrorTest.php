<?php

namespace Graphity\Tests;

use Graphity;

class ErrorTest extends \PHPUnit_Framework_TestCase
{

    const NAME = "errormessage";

    const DESC = "errordescription";

    public function test___construct()
    {
        $error = new Graphity\Error(static::NAME);
        
        $this->assertEquals(static::NAME, $error->getName());
    }

    public function getSettersTests()
    {
        return array(array("Name", static::NAME), array("Description", static::DESC));
    }

    /**
     * @dataProvider getSettersTests
     */
    public function test_setters($property, $value)
    {
        $class = new Graphity\Error(static::NAME);
        
        $setter = "set" . $property;
        $getter = "get" . $property;
        
        $class->$setter($value);
        $this->assertEquals($value, $class->$getter());
    }
}
