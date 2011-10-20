<?php

namespace Graphity\Tests\Rdf;

use Graphity\Rdf as GR;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    const TEST_URI = "http://example.org/test";
    const TEST_BNODE_ID = "bnode1";
    
    public function test___constructor_blanknode_uniqid() {
        $res = new GR\Resource();
        $this->assertTrue($res->isAnonymous());
        $this->assertTrue($res->isResource());
        $this->assertFalse($res->isLiteral());
        $this->assertFalse($res->isURIResource());
        $this->assertNull($res->getURI());
        $this->assertEquals(false, strpos($res->getURI(), "_:"));
    }
    
    public function test___constructor_blanknode_presetid() {
        $res = new GR\Resource("_:" . self::TEST_BNODE_ID);
        $this->assertTrue($res->isAnonymous());
        $this->assertTrue($res->isResource());
        $this->assertFalse($res->isLiteral());
        $this->assertFalse($res->isURIResource());
        $this->assertNull($res->getURI());
        $this->assertEquals(self::TEST_BNODE_ID, $res->getAnonymousId());
        $this->assertEquals("_:" . self::TEST_BNODE_ID, (string)$res);
    }
    
    public function test___constructor_resource() {
        $res = new GR\Resource(self::TEST_URI);
        $this->assertTrue($res->isURIResource());
        $this->assertTrue($res->isResource());
        $this->assertFalse($res->isLiteral());
        $this->assertFalse($res->isAnonymous());
        $this->assertEquals(self::TEST_URI, $res->getURI());
        $this->assertEquals(sprintf("<%s>", self::TEST_URI), (string)$res);
    }
    
    public function test_hasURI() {
        $res = new GR\Resource(self::TEST_URI);
        $this->assertFalse($res->hasURI("http://example.org/doesnt/have"));
        $this->assertTrue($res->hasURI(self::TEST_URI));
    }
}
