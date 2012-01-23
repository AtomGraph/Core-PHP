<?php

namespace Graphity\Tests\Repository;

use Graphity\Rdf\Resource;
use Graphity\Rdf\Literal;
use Graphity\Rdf\Model;
use Graphity\Rdf\Statement;
use Graphity\Repository\Client;
use Graphity\Repository\Repository;
use Graphity\Sparql\Query;
use Graphity\View\ContentType;
use Graphity\Vocabulary\XSD;


class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    const TEST_ENDPOINT_URL = "http://example.org/";

    const TEST_REPOSITORY_NAME = "example";

    private static $TRIPLE = array(
        "subject" => "http://example.org/#jhacker",
        "predicate" => "http://xmlns.com/foaf/0.1/nick",
        "objectLiteral" => "jhuckabee");

    private static $CONTENTS = "<http://example.org#jhacker> <http://xmlns.com/foaf/0.1/nick> \"jhuckabee\" .";

    private static $QUERY = "SELECT * WHERE {
        ?a ?b ?c
    }";

    private static $RESPONSE = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns:ex=\"http://example.org/#\">
  <rdf:Description rdf:about=\"http://example.org/resource1\">
      <ex:field1>value1</ex:field1>
  </rdf:Description>
  <rdf:Description rdf:about=\"http://example.org/resource1\">
      <ex:field2>value2</ex:field2>
  </rdf:Description>
</rdf:RDF>
";

    private static $ASK_RESPONSE_FMT = "<?xml version=\"1.0\"?>
<sparql xmlns=\"http://www.w3.org/2005/sparql-results#\">
    <head></head>
    <boolean>%s</boolean>
</sparql>";

    private static $ASK_BAD_RESPONSE_FMT = "<?xml version=\"1.0\"?>
<sparql xmlns=\"http://www.w3.org/2005/sparql-results#\">
    <head></head>
    <string>%s</string>
</sparql>";


    private static $COUNT_RESPONSE_FMT = "<?xml version=\"1.0\"?>
<sparql xmlns=\"http://www.w3.org/2005/sparql-results#\">
  <head>
    <variable name=\"count\"/>
  </head>
  <results>
    <result>
      <binding name=\"count\">
        <literal datatype=\"http://www.w3.org/2001/XMLSchema#integer\">%d</literal>
      </binding>
    </result>
  </results>
</sparql>";

    public function test___constructor()
    {
        $client = new Client(static::TEST_ENDPOINT_URL);
        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);

        $this->assertEquals(static::TEST_REPOSITORY_NAME, $repo->getRepositoryName());
    }

    public function test_insert_empty_model()
    {
        $client = $this->getMock("Graphity\\Repository\\Client", array('executeRequest'), array(static::TEST_ENDPOINT_URL));

        $client->expects($this->exactly(0))
               ->method('executeRequest')
               ->will($this->returnValue(array(404, "", "")));

        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);

        $this->assertFalse($repo->insert(new Model()));
    }

    function insertDataProvider() {
        return array(
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen"),
            ), 
            null,
            "INSERT DATA {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen\" .\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen", null, "en"),
            ), 
            null,
            "INSERT DATA {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen\"@en .\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen \"nicknamen\" lastnamen", null, "en"),
            ), 
            null,
            "INSERT DATA {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen \\\"nicknamen\\\" lastnamen\"@en .\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen", XSD::string),
            ), 
            null,
            "INSERT DATA {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen\"^^<http://www.w3.org/2001/XMLSchema#string> .\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen \"nicknamen\" lastnamen", XSD::string),
            ), 
            null,
            "INSERT DATA {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen \\\"nicknamen\\\" lastnamen\"^^<http://www.w3.org/2001/XMLSchema#string> .\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Resource("http://example.org/alias"),
            ), 
            null,
            "INSERT DATA {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> <http://example.org/alias> .\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen"),
            ), 
            "http://example.org/graph",
            "INSERT DATA {\nGRAPH <http://example.org/graph> {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen\" .\n}\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen", null, "en"),
            ), 
            "http://example.org/graph",
            "INSERT DATA {\nGRAPH <http://example.org/graph> {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen\"@en .\n}\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen \"nicknamen\" lastnamen", null, "en"),
            ), 
            "http://example.org/graph",
            "INSERT DATA {\nGRAPH <http://example.org/graph> {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen \\\"nicknamen\\\" lastnamen\"@en .\n}\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen", XSD::string),
            ), 
            "http://example.org/graph",
            "INSERT DATA {\nGRAPH <http://example.org/graph> {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen\"^^<http://www.w3.org/2001/XMLSchema#string> .\n}\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Literal("namen \"nicknamen\" lastnamen", XSD::string),
            ), 
            "http://example.org/graph",
            "INSERT DATA {\nGRAPH <http://example.org/graph> {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> \"namen \\\"nicknamen\\\" lastnamen\"^^<http://www.w3.org/2001/XMLSchema#string> .\n}\n}"),
            array(array(
                'subject' => new Resource("http://example.org/name-nameson#1"),
                'predicate' => new Resource("http://xmlns.com/foaf/0.1/nick"),
                'object' => new Resource("http://example.org/alias"),
            ), 
            "http://example.org/graph",
            "INSERT DATA {\nGRAPH <http://example.org/graph> {\n<http://example.org/name-nameson#1> <http://xmlns.com/foaf/0.1/nick> <http://example.org/alias> .\n}\n}"),
        );
    }

    /**
     * @dataProvider insertDataProvider
     */
    function test_insert_with_single_triple($triple, $graph, $expectedValue)
    {
        $client = $this->getMock("Graphity\\Repository\\Client", array("executeRequest"), array(static::TEST_ENDPOINT_URL));

        $client->expects($this->once())
               ->method("executeRequest")
               ->will($this->returnValue(array(200, "", "")));

        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);

        $model = new Model();
        $model->addStatement(new Statement($triple['subject'], $triple['predicate'], $triple['object']));

        /** Assert response */
        $this->assertTrue($repo->insert($model, $graph));

        /** Assert request */
        $this->assertEquals("POST", $client->getMethod());
        $this->assertEquals("/" . static::TEST_REPOSITORY_NAME . "/sparql", $client->getPath());
        $this->assertArrayHasKey("Accept", $client->getAllHeaders());
        $this->assertArrayHasKey("Content-Type", $client->getAllHeaders());
        $this->assertEquals(ContentType::APPLICATION_SPARQL_XML, $client->getHeader("Accept"));
        $this->assertContains(ContentType::APPLICATION_SPARQL_UPDATE, $client->getAllHeaders());
        $this->assertEquals($expectedValue, $client->getData());
    }

    /**
     * @dataProvider insertDataProvider
     * @expectedException Graphity\WebApplicationException
     */
    public function test_insert_bad_response($triple, $graph, $expectedValue)
    {
        $client = $this->getMock("Graphity\\Repository\\Client", array("executeRequest"), array(static::TEST_ENDPOINT_URL));

        $client->expects($this->once())
               ->method("executeRequest")
               ->will($this->returnValue(array(400, "", "")));

        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);

        $model = new Model();
        $model->addStatement(new Statement($triple['subject'], $triple['predicate'], $triple['object']));

        /** Assert response */
        $repo->insert($model, $graph);
    }

    public function test_query()
    {
        $client = $this->getMock("Graphity\\Repository\\Client", array("executeRequest"), array(static::TEST_ENDPOINT_URL));

        $client->expects($this->once())
               ->method("executeRequest")
               ->will($this->returnValue(array(200, self::$RESPONSE, "")));

        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);

        $this->assertEquals(self::$RESPONSE, $repo->query(Query::newInstance()->setQuery(self::$QUERY)));

        $this->assertEquals("GET", $client->getMethod());
        $this->assertEquals("/" . static::TEST_REPOSITORY_NAME . "/sparql", $client->getPath());
        $this->assertEquals(ContentType::APPLICATION_RDF_XML, $client->getHeader("Accept"));
        $this->assertContains(self::$QUERY, $client->getData());
        $this->assertArrayHasKey("query", $client->getData());
    }

    public function test_query_long_query()
    {
        $longQuery = "SELECT * WHERE {\n";
        while(strlen($longQuery) < 2000) {
            $longQuery .= "\t?a ?b ?c .\n";
        }
        $longQuery .= "}";

        $client = $this->getMock("Graphity\\Repository\\Client", array("executeRequest"), array(static::TEST_ENDPOINT_URL));

        $client->expects($this->once())
               ->method("executeRequest")
               ->will($this->returnValue(array(200, self::$RESPONSE, "")));

        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);

        $this->assertEquals(self::$RESPONSE, $repo->query(Query::newInstance()->setQuery($longQuery)));

        $this->assertEquals("POST", $client->getMethod());
        $this->assertEquals("/" . static::TEST_REPOSITORY_NAME . "/sparql", $client->getPath());
        $this->assertEquals(ContentType::APPLICATION_RDF_XML, $client->getHeader("Accept"));
        $this->assertEquals("query=" . urlencode($longQuery), $client->getData());
    }

    /**
     * @expectedException Graphity\WebApplicationException
     */
    public function test_query_bad_response()
    {
        $client = $this->getMock("Graphity\\Repository\\Client", array("executeRequest"), array(static::TEST_ENDPOINT_URL));

        $client->expects($this->once())
               ->method("executeRequest")
               ->will($this->returnValue(array(400, self::$RESPONSE, "")));

        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);

        $repo->query(Query::newInstance()->setQuery(self::$QUERY));
    }

    public function test_update()
    {
        $client = $this->getMock("Graphity\\Repository\\Client", array("executeRequest"), array(static::TEST_ENDPOINT_URL));

        $client->expects($this->once())
               ->method("executeRequest")
               ->will($this->returnValue(array(200, self::$RESPONSE, "")));

        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);
        
        $repo->update(Query::newInstance()->setQuery("...")); 
    }

    public function askProvider_invalid()
    {
        $invalidQuery = "PREFIX ex: <http://example.org/>\nSELECT * {?a ?b ?c}";
        $validQuery = "PREFIX ex: <http://example.org/>\n ASK * {?a ?b ?c}";

        $badResponse = sprintf(self::$ASK_BAD_RESPONSE_FMT, "true");

        return array(
            array(self::$RESPONSE, $invalidQuery, 0),
            array("", $validQuery, 1),
            array(self::$RESPONSE, $validQuery, 1),
            array($badResponse, $validQuery, 1),
        );
    }
    /**
     * @dataProvider askProvider_invalid
     * @expectedException Graphity\WebApplicationException
     */
    public function test_ask_invalid($response, $query, $executeRequest)
    {
        $client = $this->getMock("Graphity\\Repository\\Client", array("executeRequest"), array(static::TEST_ENDPOINT_URL));

        $client->expects($this->exactly($executeRequest))
               ->method("executeRequest")
               ->will($this->returnValue(array(200, $response, "")));

        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);

        $repo->ask(Query::newInstance()->setQuery($query));
    }

    public function askProvider()
    {
        return array(
            array("true", true),
            array("false", false),
        );
    }

    /**
     * @dataProvider askProvider
     */
    public function test_ask($response, $expected)
    {
        $client = $this->getMock("Graphity\\Repository\\Client", array("executeRequest"), array(static::TEST_ENDPOINT_URL));

        $client->expects($this->once())
               ->method("executeRequest")
               ->will($this->returnValue(array(200, sprintf(self::$ASK_RESPONSE_FMT, $response), "")));

        $repo = new Repository($client, static::TEST_REPOSITORY_NAME);

        $this->assertEquals($expected, $repo->ask(Query::newInstance()->setQuery("PREFIX ex: <http://example.org/>\nASK * {?a ?b ?c}")));
    }
}

