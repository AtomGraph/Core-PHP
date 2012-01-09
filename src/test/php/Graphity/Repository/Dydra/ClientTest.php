<?php

namespace Graphity\Tests\Repository\Dydra;

use Graphity\Repository\Dydra\Client;
use Graphity\View\ContentType;
use Graphity\WebClientException;

class ClientExposed extends Client
{
    public function getURL()
    {
        return parent::getURL();
    }
}

class ClientTest extends \PHPUnit_Framework_TestCase
{
    const TEST_ENDPOINT_URL = 'http://example.org/';

    const TEST_AUTH_TOKEN = "1234567890";

    public function test___constructor()
    {
        $client = new Client(static::TEST_ENDPOINT_URL, static::TEST_AUTH_TOKEN);
        $this->assertEquals(static::TEST_ENDPOINT_URL, $client->getEndpointUrl());
        $this->assertEquals(static::TEST_AUTH_TOKEN, $client->getAuthToken());
    }

    public function getURLProvider()
    {
        return array(
            /** $path, $data, $method, $expectedUrl (without endpointUrl) */
            array(null, null, "GET", "?auth_token=" . static::TEST_AUTH_TOKEN),
            array("/repository/query", null, "GET", "/repository/query?auth_token=" . static::TEST_AUTH_TOKEN),
            array("/repository/query", array('key1' => 'value1', 'key2' => 'encoded value%'), "GET", "/repository/query?key1=value1&key2=encoded+value%25&auth_token=" . static::TEST_AUTH_TOKEN),
            array("/repository/query", ("key1=value1&key2=" . urlencode("encoded value%")), "GET", "/repository/query?key1=value1&key2=encoded+value%25&auth_token=" . static::TEST_AUTH_TOKEN),
            array("/repository/query?something=here", ("key1=value1&key2=" . urlencode("encoded value%")), "GET", "/repository/query?something=here&key1=value1&key2=encoded+value%25&auth_token=" . static::TEST_AUTH_TOKEN),

            array(null, null, "POST", "?auth_token=" . static::TEST_AUTH_TOKEN),
            array("/repository/query", null, "POST", "/repository/query?auth_token=" . static::TEST_AUTH_TOKEN),
            array("/repository/query", array('key1' => 'value1', 'key2' => 'encoded value%'), "POST", "/repository/query?auth_token=" . static::TEST_AUTH_TOKEN),
            array("/repository/query", ("key1=value1&key2=" . urlencode("encoded value%")), "POST", "/repository/query?auth_token=" . static::TEST_AUTH_TOKEN),
        );
    }

    /**
     * @dataProvider getURLProvider
     */
    public function test_getURL($path, $data, $method, $expectedUrl)
    {
        $client = new ClientExposed(static::TEST_ENDPOINT_URL, static::TEST_AUTH_TOKEN);
        $client->setPath($path)
               ->setMethod($method)
               ->setHeader("Accept", ContentType::APPLICATION_RDF_XML)
               ->setData($data);

        $this->assertEquals($method, $client->getMethod());
        $this->assertEquals($data, $client->getData());
        $this->assertEquals(static::TEST_ENDPOINT_URL . ltrim($expectedUrl, "/"), $client->getURL());
        $this->assertEquals(ContentType::APPLICATION_RDF_XML, $client->getHeader("Accept"));
    }
}

