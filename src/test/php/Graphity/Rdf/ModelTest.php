<?php

namespace Graphity\Tests\Rdf;

use Graphity\Rdf as GR;
use Graphity\Vocabulary\Rdf;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    const TEST_ACCOUNT_ID = "test";
    const TEST_AUTH_TOKEN = "test";
    const TEST_ENDPOINT_URL = "http://localhost/query";
    
    protected static $ARRAY = null;
    
    protected static $STATEMENT = null;

    protected static $DOM = null;
    
    const TURTLE = '<http://example.org/item/1> <http://rdf.org/#type> "test:TestPost" .
<http://example.org/item/1> <http://foaf.org/#depiction> <http://example.org/images/test.png> .
<http://example.org/item/1> <http://sioc.org/#content> "Example Test Post Content" .
<http://example.org/item/1> <http://dct.org/#hasPart> _:b1 .
_:b1 <http://rdf.org/#first> <http://example.org/item/1#sub1> .
_:b1 <http://rdf.org/#rest> _:b2 .
_:b2 <http://rdf.org/#first> <http://example.org/item/1#sub2> .
_:b2 <http://rdf.org/#rest> <http://rdf.org/#nil> .
';
    
    public function setUp() {
        self::$ARRAY = array(
            new GR\Statement(new GR\Resource("http://example.org/item/1"), new GR\Resource("http://rdf.org/#type"), new GR\Literal("test:TestPost")),
            new GR\Statement(new GR\Resource("http://example.org/item/1"), new GR\Resource("http://foaf.org/#depiction"), new GR\Resource("http://example.org/images/test.png")),
            new GR\Statement(new GR\Resource("http://example.org/item/1"), new GR\Resource("http://sioc.org/#content"), new GR\Literal("Example Test Post Content")),
            new GR\Statement(new GR\Resource("http://example.org/item/1"), new GR\Resource("http://dct.org/#hasPart"), new GR\Resource("_:b1")),
            new GR\Statement(new GR\Resource("_:b1"), new GR\Resource("http://rdf.org/#first"), new GR\Resource("http://example.org/item/1#sub1")),
            new GR\Statement(new GR\Resource("_:b1"), new GR\Resource("http://rdf.org/#rest"), new GR\Resource("_:b2")),
            new GR\Statement(new GR\Resource("_:b2"), new GR\Resource("http://rdf.org/#first"), new GR\Resource("http://example.org/item/1#sub2")),
            new GR\Statement(new GR\Resource("_:b2"), new GR\Resource("http://rdf.org/#rest"), new GR\Resource("http://rdf.org/#nil")),
        );
        
        self::$STATEMENT = new GR\Statement(new GR\Resource("http://example.org/item/1"), new GR\Resource("http://sioc.org/#title"), new GR\Literal("Funny"));

        self::$DOM = new \DOMDocument("1.0", "UTF-8");
        $rdfElem = self::$DOM->appendChild(self::$DOM->createElementNS(Rdf::NS, "rdf:RDF"));

        $item1Elem = $rdfElem->appendChild(self::$DOM->createElementNS(Rdf::NS, "rdf:Description"));
        $item1Elem->setAttributeNS(Rdf::NS, "rdf:about", "http://example.org/item/1");
        $typeElem = $item1Elem->appendChild(self::$DOM->createElementNS("http://rdf.org/#", "tmp:type")); // properties get tmp: prefix
        $typeElem->appendChild(self::$DOM->createTextNode("test:TestPost"));
        $depictionElem = $item1Elem->appendChild(self::$DOM->createElementNS("http://foaf.org/#", "tmp:depiction"));
        $depictionElem->setAttributeNS(Rdf::NS, "rdf:resource", "http://example.org/images/test.png");
        $contentElem = $item1Elem->appendChild(self::$DOM->createElementNS("http://sioc.org/#", "tmp:content"));
        $contentElem->appendChild(self::$DOM->createTextNode("Example Test Post Content"));
        $hasPartElem = $item1Elem->appendChild(self::$DOM->createElementNS("http://dct.org/#", "tmp:hasPart"));
        $hasPartElem->setAttributeNS(Rdf::NS, "rdf:nodeID", "b1");

        $bnode1Elem = $rdfElem->appendChild(self::$DOM->createElementNS(Rdf::NS, "rdf:Description"));
        $bnode1Elem->setAttributeNS(Rdf::NS, "rdf:nodeID", "b1");
        $first1Elem = $bnode1Elem->appendChild(self::$DOM->createElementNS("http://rdf.org/#", "tmp:first"));
        $first1Elem->setAttributeNS(Rdf::NS, "rdf:resource", "http://example.org/item/1#sub1");
        $rest1Elem = $bnode1Elem->appendChild(self::$DOM->createElementNS("http://rdf.org/#", "tmp:rest"));
        $rest1Elem->setAttributeNS(Rdf::NS, "rdf:nodeID", "b2");

        $bnode2Elem = $rdfElem->appendChild(self::$DOM->createElementNS(Rdf::NS, "rdf:Description"));
        $bnode2Elem->setAttributeNS(Rdf::NS, "rdf:nodeID", "b2");
        $first2Elem = $bnode2Elem->appendChild(self::$DOM->createElementNS("http://rdf.org/#", "tmp:first"));
        $first2Elem->setAttributeNS(Rdf::NS, "rdf:resource", "http://example.org/item/1#sub2");
        $rest2Elem = $bnode2Elem->appendChild(self::$DOM->createElementNS("http://rdf.org/#", "tmp:rest"));
        $rest2Elem->setAttributeNS(Rdf::NS, "rdf:resource", "http://rdf.org/#nil");
    }
    
    public function test_addArray() {
        $model = new GR\Model();
        
        $this->assertTrue(count($model->getStatements()) === 0);
        
        $model->addStatement(self::$STATEMENT);
        $model->addArray(self::$ARRAY);
        
        $listOfStmts = $model->getStatements();
        $this->assertTrue(count($listOfStmts) === (1 + count(self::$ARRAY)));
        $this->assertEquals($listOfStmts[0]->getSubject(), self::$STATEMENT->getSubject());
        $this->assertEquals($listOfStmts[0]->getPredicate(), self::$STATEMENT->getPredicate());
        $this->assertEquals($listOfStmts[0]->getObject(), self::$STATEMENT->getObject());
        
        for($i = 0; $i < count(self::$ARRAY); $i++) {
            $this->assertEquals($listOfStmts[$i + 1]->getSubject(), self::$ARRAY[$i]->getSubject());
            $this->assertEquals($listOfStmts[$i + 1]->getPredicate(), self::$ARRAY[$i]->getPredicate());
            $this->assertEquals($listOfStmts[$i + 1]->getObject(), self::$ARRAY[$i]->getObject());
        }
    }
    
    public function test_addStatement() {
        $model = new GR\Model();
        
        $this->assertTrue(count($model->getStatements()) === 0);
        $model->addStatement(self::$STATEMENT);
        $this->assertTrue(count($model->getStatements()) === 1);
    }

    public function test_contains() {
        $model = new GR\Model();
        
        $this->assertTrue(count($model->getStatements()) === 0);

        $model->addArray(self::$ARRAY);
        
        foreach(self::$ARRAY as $stmt) {
            $this->assertTrue($model->contains($stmt->getSubject(), $stmt->getPredicate()));
        }

        $this->assertFalse($model->contains(new GR\Resource("http://random.org/url"), new GR\Resource("http://random.org/type")));
    }

    public function test_containsStatement() {
        $model = new GR\Model();
      
        $this->assertTrue(count($model->getStatements()) === 0);

        $model->addArray(self::$ARRAY);

        foreach(self::$ARRAY as $stmt) {
            $this->assertTrue($model->containsStatement($stmt));
        }

        $this->assertFalse($model->containsStatement(new GR\Statement(new GR\Resource("http://random.org/url"), new GR\Resource("http://random.org/type"), new GR\Literal("random text"))));
    }

    public function test_containsResource() {
        $model = new GR\Model();
      
        $this->assertTrue(count($model->getStatements()) === 0);

        $model->addArray(self::$ARRAY);

        foreach(self::$ARRAY as $stmt) {
            $this->assertTrue($model->containsResource($stmt->getSubject()));
            $this->assertTrue($model->containsResource($stmt->getPredicate()));
        }

        $this->assertFalse($model->containsResource(new GR\Resource("http://random.org/url")));
    }

    public function test_listResourcesWithProperty() {
        $model = new GR\Model();

        $this->assertTrue(count($model->getStatements()) === 0);

        $model->addArray(self::$ARRAY);

        $array = $model->listResourcesWithProperty(new GR\Resource("http://rdf.org/#type"), new GR\Literal("test:TestPost"));
        $this->assertTrue(count($array) === 1);
    }
    
    public function test_getStatements() {
        $model = new GR\Model();
        
        $this->assertTrue(count($model->getStatements()) === 0);
        
        $model->addArray(self::$ARRAY);
        
        $listOfStmts = $model->getStatements();
        $this->assertTrue(count($listOfStmts) === count(self::$ARRAY));
        
        for($i = 0; $i < count(self::$ARRAY); $i++) {
            $this->assertEquals($listOfStmts[$i]->getSubject(), self::$ARRAY[$i]->getSubject());
            $this->assertEquals($listOfStmts[$i]->getPredicate(), self::$ARRAY[$i]->getPredicate());
            $this->assertEquals($listOfStmts[$i]->getObject(), self::$ARRAY[$i]->getObject());
        }
    }
    
    public function test_removeArray() {
        $model = new GR\Model();
        
        $this->assertTrue(count($model->getStatements()) === 0);
        
        $model->addStatement(self::$STATEMENT);
        
        $this->assertTrue(count($model->getStatements()) === 1);
        
        $model->addArray(self::$ARRAY);
        
        $this->assertTrue(count($model->getStatements()) === (1 + count(self::$ARRAY)));
        
        $model->removeArray(self::$ARRAY);
        
        $listOfStmts = $model->getStatements();
        $this->assertTrue(count($listOfStmts) === 1);
        $this->assertEquals($listOfStmts[0]->getSubject(), self::$STATEMENT->getSubject());
        $this->assertEquals($listOfStmts[0]->getPredicate(), self::$STATEMENT->getPredicate());
        $this->assertEquals($listOfStmts[0]->getObject(), self::$STATEMENT->getObject());
    }
    
    public function test_removeStatement() {
        $model = new GR\Model();
        
        $this->assertTrue(count($model->getStatements()) === 0);
        
        $model->addArray(self::$ARRAY);
        
        $this->assertTrue(count($model->getStatements()) === count(self::$ARRAY));
        
        for($i = 0; $i < count(self::$ARRAY); $i++) {
            $model->removeStatement(self::$ARRAY[$i]);
            
            for($j = $i; $j < count(self::$ARRAY) - 1; $j++) {
                $listOfStmts = $model->getStatements();
                $this->assertEquals($listOfStmts[$j - $i]->getSubject(), self::$ARRAY[$j+1]->getSubject());
                $this->assertEquals($listOfStmts[$j - $i]->getPredicate(), self::$ARRAY[$j+1]->getPredicate());
                $this->assertEquals($listOfStmts[$j - $i]->getObject(), self::$ARRAY[$j+1]->getObject());
            }
        }
        
        $this->assertTrue(count($model->getStatements()) === 0);
    }
    
    public function test_removeAll() {
        $model = new GR\Model();
        
        $this->assertTrue(count($model->getStatements()) === 0);
        
        $model->addStatement(self::$STATEMENT);
        $model->removeAll();
        
        $this->assertTrue(count($model->getStatements()) === 0);
        
        $model->addArray(self::$ARRAY);
        $model->removeAll();
        
        $this->assertTrue(count($model->getStatements()) === 0);
        
        $model->addStatement(self::$STATEMENT);
        $model->addArray(self::$ARRAY);
        $model->removeAll();
        
        $this->assertTrue(count($model->getStatements()) === 0);
    }
    
    public function test___toString() {
        $model = new GR\Model();
        $model->addArray(self::$ARRAY);
        $this->assertEquals(self::TURTLE, (string)$model);
    }

    public function test_toDOM() {
        $model = new GR\Model();
        $model->addArray(self::$ARRAY);
        //var_dump(self::$DOM->saveXML()); 
        //var_dump($model->toDOM()->saveXML()); exit;
        $this->assertEqualXMLStructure(self::$DOM->documentElement, $model->toDOM()->documentElement);
    }

}
