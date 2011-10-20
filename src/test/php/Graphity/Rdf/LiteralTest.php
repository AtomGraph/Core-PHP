<?php

namespace Graphity\Tests\Rdf;

use Graphity\Rdf as GR;

class LiteralTest extends \PHPUnit_Framework_TestCase
{
    const TEST_URI = "http://example.org/issue/1";
    const TEST_VALUE = "test_value";
    const TEST_TYPE = "xsd:string";
    const TEST_LANG = "en";
    const TEST_ESCAPED_VALUE = "this is \"escaped\" string";
    const TEST_LONG_STRING = "\tSOME \"VERY\" LONG\nSTRING";
    
    public function test___constructor_stringliteral() {
        $literal = new GR\Literal(self::TEST_VALUE, self::TEST_TYPE, null);
        $this->assertEquals(sprintf("\"%s\"^^%s", self::TEST_VALUE, self::TEST_TYPE), (string)$literal);
        
        $literal = new GR\Literal(self::TEST_VALUE, null, self::TEST_LANG);
        $this->assertEquals(sprintf("\"%s\"@%s", self::TEST_VALUE, self::TEST_LANG), (string)$literal);
        
        $literal = new GR\Literal(self::TEST_VALUE, null, null);
        $this->assertEquals(sprintf("\"%s\"", self::TEST_VALUE), (string)$literal);
        
        $literal = new GR\Literal(self::TEST_ESCAPED_VALUE);
        $this->assertEquals(self::TEST_ESCAPED_VALUE, $literal->getValue());

        $literal = new GR\Literal(self::TEST_LONG_STRING);
        $this->assertEquals(sprintf("\"\"\"%s\"\"\"", self::TEST_LONG_STRING), (string)$literal);
    }
}
