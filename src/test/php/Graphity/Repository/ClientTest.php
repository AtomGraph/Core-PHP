<?php

namespace Graphity\Tests\Repository;

use Graphity\Repository\Client;
use Graphity\WebClientException;
use Graphity\View\ContentType;

class ClientExposed extends Client
{

    public function getURL()
    {
        return parent::getURL();
    }

    public function prepareHTTPHeaders()
    {
        return parent::prepareHTTPHeaders();
    }
}

class ClientTest extends \PHPUnit_Framework_TestCase
{
    const TEST_ENDPOINT_URL = 'http://example.org/';

    public function test___constructor_with_parameters()
    {
        $client = new Client(static::TEST_ENDPOINT_URL);
        $this->assertEquals(static::TEST_ENDPOINT_URL, $client->getEndpointUrl());
    }

    public function test_reset()
    {
        $client = new Client(static::TEST_ENDPOINT_URL);
        $client->setPath("/" . $client->getEndpointUrl() . "/query")->setMethod("POST")->setData(array('subject' => 'test', 'object' => 'test'))->setHeader("Content-Type", ContentType::APPLICATION_XML)->setHeader("Accept", ContentType::APPLICATION_RDF_XML)->reset();
        
        $this->assertEquals("GET", $client->getMethod());
        $this->assertNull($client->getPath());
        $this->assertNull($client->getData());
        $this->assertEmpty($client->getAllHeaders());
    }

    public function getURLProvider()
    {
        return array(
            /** $path, $data, $method, $expectedUrl (without endpointUrl) */
            array(null, null, "GET", ""),
            array("/repository/query", null, "GET", "/repository/query"),
            array("/repository/query", array('key1' => 'value1', 'key2' => 'encoded value%'), "GET", "/repository/query?key1=value1&key2=encoded+value%25"),
            array("/repository/query", ("key1=value1&key2=" . urlencode("encoded value%")), "GET", "/repository/query?key1=value1&key2=encoded+value%25"),

            array(null, null, "POST", ""),
            array("/repository/query", null, "POST", "/repository/query"),
            array("/repository/query", array('key1' => 'value1', 'key2' => 'encoded value%'), "POST", "/repository/query"),
            array("/repository/query", ("key1=value1&key2=" . urlencode("encoded value%")), "POST", "/repository/query"),
        );
    }

    /**
     * @dataProvider getURLProvider
     */
    public function test_getURL($path, $data, $method, $expectedUrl)
    {
        $client = new ClientExposed(static::TEST_ENDPOINT_URL);
        $client->setPath($path)
               ->setMethod($method)
               ->setHeader("Accept", ContentType::APPLICATION_RDF_XML)
               ->setData($data);

        $this->assertEquals($method, $client->getMethod());
        $this->assertEquals($data, $client->getData());
        $this->assertEquals(static::TEST_ENDPOINT_URL . ltrim($expectedUrl, "/"), $client->getURL());
        $this->assertEquals(ContentType::APPLICATION_RDF_XML, $client->getHeader("Accept"));
    }

    public function prepareHTTPHeadersProvider()
    {
        return array(
            array("Accept", "text/plain"),
            array("Accept", "*/*"),
            array("Content-Type", "application/rdf+xml"),
            array("Content-Type", "application/json"), 
        );
    }

    /**
     * @dataProvider prepareHTTPHeadersProvider
     */
    public function test_prepareHTTPHeaders($name, $value)
    {
        $client = new ClientExposed(static::TEST_ENDPOINT_URL);
        $client->setHeader($name, $value);
        $this->assertContains($name . ": " . $value, $client->prepareHTTPHeaders());
        $this->assertEquals(1, count($client->prepareHTTPHeaders()));
        $client->reset();
        $this->assertEmpty($client->prepareHTTPHeaders());
    }

    function test_setMethod_valid()
    {
        $client = new ClientExposed(static::TEST_ENDPOINT_URL);
        
        $client->setMethod("GET");
        $this->assertEquals("GET", $client->getMethod());
        $client->setMethod("POST");
        $this->assertEquals("POST", $client->getMethod());
        $client->setMethod("PUT");
        $this->assertEquals("PUT", $client->getMethod());
        $client->setMethod("DELETE");
        $this->assertEquals("DELETE", $client->getMethod());
    }

    /**
     * @expectedException Graphity\WebApplicationException
     */
    function test_setMethod_exception()
    {
        $client = new ClientExposed(static::TEST_ENDPOINT_URL);
        
        $client->setMethod("INVALID");
    }
}