<?php

namespace Graphity\Tests\Rdf;

use Graphity\Rdf as GR;

class StatementTest extends \PHPUnit_Framework_TestCase
{
    const TEST_SUBJECT = "http://example.org/issue/1";
    const TEST_PREDICATE = "http://rdf.org/#type";
    const TEST_OBJECT = "test:TestPost";
    const TEST_URI = "http://example.org/issue/1#test";
    
    function test___constructor_with_parameters()
    {
        $stmt = new GR\Statement(new GR\Resource(self::TEST_SUBJECT), new GR\Resource(self::TEST_PREDICATE), new GR\Literal(self::TEST_OBJECT));
        $this->assertEquals(self::TEST_SUBJECT, $stmt->getSubject()->getURI());
        $this->assertEquals(self::TEST_PREDICATE, $stmt->getPredicate()->getURI());
        $this->assertEquals(sprintf("\"%s\"", self::TEST_OBJECT), (string)$stmt->getObject());
        
        $stmt = new GR\Statement(new GR\Resource(self::TEST_SUBJECT), new GR\Resource(self::TEST_PREDICATE), new GR\Resource(self::TEST_URI));
        $this->assertEquals(self::TEST_SUBJECT, $stmt->getSubject()->getURI());
        $this->assertEquals(self::TEST_PREDICATE, $stmt->getPredicate()->getURI());
        $this->assertEquals(sprintf("<%s>", self::TEST_URI), (string)$stmt->getObject());
    }
    
    function providerSettersGetters() {
        return array(
            array('Subject', new GR\Resource("http://example.org/issues/2"), "<http://example.org/issues/2>"),
            array('Predicate', new GR\Resource("http://rdf.org/#type"), "<http://rdf.org/#type>"),
            array('Object', new GR\Literal(self::TEST_OBJECT), "\"" . self::TEST_OBJECT . "\"")
        );
    }
    
    /**
     * @dataProvider providerSettersGetters
     */
    function test_settersGetters($name, $value, $expectedValue) {
        $stmt = new GR\Statement(new GR\Resource(self::TEST_SUBJECT), new GR\Resource(self::TEST_PREDICATE), new GR\Literal(self::TEST_OBJECT));
        
        $setter = "set" . $name;
        $getter = "get" . $name;
        
        $stmt->$setter($value);
        $this->assertEquals($expectedValue, (string)($stmt->$getter()));
    }
    
    function test___toString() {
        $stmt = new GR\Statement(new GR\Resource(self::TEST_SUBJECT), new GR\Resource(self::TEST_PREDICATE), new GR\Literal(self::TEST_OBJECT));
        $expected = sprintf("<%s> <%s> \"%s\" .\n", self::TEST_SUBJECT, self::TEST_PREDICATE, self::TEST_OBJECT);
        
        $this->assertEquals($expected, (string)$stmt);
    }
}
