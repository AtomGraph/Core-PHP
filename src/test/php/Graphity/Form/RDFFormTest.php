<?php

namespace Graphity\Tests;

use Graphity;

class RDFFormTest extends \PHPUnit_Framework_TestCase
{

    static $POST_BODY = null;

    static $STMTS = null;

    public function setUp()
    {
        self::$POST_BODY = "&rdf=&su=" . urlencode("http://subject1") . "&pu=" . urlencode("http://dc.org/#title") . "&ol=" . urlencode("title") . "&ll=da" .
                                "&su=" . urlencode("http://subject1") . "&pu=" . urlencode("http://predicate1") . "&ou=" . urlencode("http://object1") . 
                                                                        "&pu=" . urlencode("http://predicate2") . "&ou=" . urlencode("http://object2") . 
                                                                                                                  "&ou=" . urlencode("http://object3") . 
                                "&su=" . urlencode("http://subject2") . "&pu=" . urlencode("http://predicate3") . "&ol=" . urlencode("literal1") . 
                                "&su=" . urlencode("http://subject3") . "&pu=" . urlencode("http://predicate4") . "&ol=" . urlencode("literal2") . "&ll=da" . 
                                "&su=" . urlencode("http://subject4") . "&pu=" . urlencode("http://predicate5") . "&ol=" . urlencode("literal3") . "&lt=" . urlencode("http://type") . 
                                                                        "&pu=" . urlencode("http://dct.org/#hasPart") . "&ob=" . urlencode("b1") . 
                                "&sb=" . urlencode("b1") . "&pu=" . urlencode("http://rdf.org/#first") . "&ou=" . urlencode("http://something/") . 
                                                           "&pu=" . urlencode("http://rdf.org/#rest") . "&ou=" . urlencode("http://rdf.org/#nil");
        
        self::$STMTS = array(
            "<http://subject1> <http://dc.org/#title> \"title\"@da .\n",
        	"<http://subject1> <http://predicate1> <http://object1> .\n", 
        	"<http://subject1> <http://predicate2> <http://object2> .\n", 
        	"<http://subject1> <http://predicate2> <http://object3> .\n", 
        	"<http://subject2> <http://predicate3> \"literal1\" .\n", 
        	"<http://subject3> <http://predicate4> \"literal2\"@da .\n", 
        	"<http://subject4> <http://predicate5> \"literal3\"^^<http://type> .\n", 
        	"<http://subject4> <http://dct.org/#hasPart> _:b1 .\n", 
        	"_:b1 <http://rdf.org/#first> <http://something/> .\n", 
        	"_:b1 <http://rdf.org/#rest> <http://rdf.org/#nil> .\n");
    }

    public function test_getStatements()
    {
        $stream = fopen('data://text/plain,' . self::$POST_BODY, "r");
        
        $form = $this->getMockBuilder('Graphity\\Form\\RDFForm')
                     ->disableOriginalConstructor()
                     ->setMethods(array('getInputStream'))
                     ->getMock();
        $form->expects($this->any())
             ->method('getInputStream')
             ->will($this->returnValue($stream));
        $form->__construct();
        
        $model = $form->getModel();
        $this->assertEquals(count(self::$STMTS), count($model->getStatements()));
        foreach($model->getStatements() as $idx => $stmt) {
            $this->assertEquals(self::$STMTS[$idx], (string)$stmt);
        }

        $this->assertEquals(implode("", self::$STMTS), (string)$model);
    }

}
