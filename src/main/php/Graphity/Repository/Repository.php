<?php

/**
 *  Copyright 2011 Graphity Team
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *  @package        graphity
 *  @author         Julius Šėporaitis <julius@seporaitis.net>
 *  @link           http://graphity.org/
 */

namespace Graphity\Repository;

use Graphity\Rdf\Model;
use Graphity\Sparql\Query;
use Graphity\View\ContentType;
use Graphity\WebApplicationException;


/**
 * Generic SPARQL repository wrapper.
 */
class Repository implements RepositoryInterface
{
    /**
     * @var Graphity\Repository\Client $client
     */
    private $client = null;

    /**
     * @var string $repositoryName Repository Name
     */
    private $repositoryName = null;

    /**
     * @var array $actionPaths Array mapping different endpoint pathendings 
     * for different methods.
     */
    private $actionPaths = array();

    /**
     * Construct the repository wrapper instance.
     *
     * @param Graphity\Repository\Client $client
     * @param string $repositoryName
     * @param array $actionPaths
     */
    public function __construct(Client $client, $repositoryName, 
        $actionPaths = array('insert' => 'sparql', 'query' => 'sparql', 'update' => 'sparql'))
    {
        $this->client = $client;
        $this->repositoryName = $repositoryName;
        $this->actionPaths = $actionPaths;
    }

    /**
     * Insert an Rdf\Model serialization into the Repository.
     *
     * You can also specify an optional graph URI.
     *
     * @param Rdf\Model $model  Model instance
     * @param string $graph     Optional graph URI.
     *
     * @return boolean
     */
    public function insert(Model $model, $graph = null)
    {
        if(count($model->getStatements()) === 0) {
            return false;
        }

        $preparedQuery = "";
        if($graph === null) {
            $preparedQuery = sprintf("INSERT DATA {\n%s}", (string)$model);
        } else {
            $preparedQuery = sprintf("INSERT DATA {\nGRAPH <%s> {\n%s}\n}", $graph, (string)$model);
        }

        $client = $this->getClient()
            ->reset()
            ->setPath('/' . $this->getRepositoryName() . '/' . $this->getActionPath("insert"))
            ->setMethod("POST")
            ->setHeader("Content-Type", "text/plain; charset=utf-8")
            ->setHeader("Accept", ContentType::APPLICATION_RDF_XML)
            ->setData($preparedQuery);

        list($responseCode, $body, $headers) = $client->executeRequest();

        if(!in_array($responseCode, array(200, // OK
            201, // Created
            204))) { // No Content
            throw new WebApplicationException("Could not insert data into repository: " . $this->getRepositoryName());
        }

        return true;
    }

    /**
     * Query the Repository with a SPARQL query.
     *
     * Should try to retrieve RDF/XML and return values grouped by
     * resource.
     *
     * @param Graphity\Sparql\Query $query   Query instance
     *
     * @throws Graphity\Repository\Exception in case of error.
     *
     * @return string
     */
    public function query(Query $query)
    {
        return $this->_query($query, 'query', 'GET', ContentType::APPLICATION_RDF_XML);

        // make sure triples are grouped by resource uri.
        $doc = new \DOMDocument("1.0", "UTF-8");
        $doc->loadXML($result);
        $result= XSLTBuilder::fromStylesheetURI((dirname(__FILE__) . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "webapp" . DIRECTORY_SEPARATOR . "WEB-INF" . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "group-triples.xsl"))->document($doc)->buildXML();

        return $result;
    }

    /**
     * Update data in the repository with a SPARQL query.
     *
     * @param Sparql\Query $query   Query instance
     *
     * @throws Graphity\Sparql\Repository\Exception in case of error.
     *
     * @return string
     */
    public function update(Query $query)
    {
        return $this->_query($query, 'update', 'POST', ContentType::APPLICATION_RDF_XML);
    }

    /**
     * Execute SPARQL COUNT query.
     *
     * @param Graphity\Sparql\Query $query
     *
     * @return boolean
     */
    public function count(Query $query)
    {
        $numOfMatches = preg_match('/(?<query>COUNT([^}]+)})/ims', $query);
        if($numOfMatches === 0) {
            throw new WebApplicationException("Could not find COUNT statement in SPARQL query: '" . str_ireplace("\n", "\\n", $query));
        }
        if(strpos($query, "?count") === false) {
            throw new WebApplicationException("Could not find ?count binding in SPARQL query: '" . str_ireplace("\n", "\\n", $query));
        }

        $response = $this->_query($query, 'query', 'GET', ContentType::APPLICATION_SPARQL_XML);

        if(empty($response)) {
            throw new WebApplicationException("Invalid count response from repository.");
        }

        $document = new \DOMDocument("1.0", "utf-8");
        if(@$document->loadXML($response) === false) {
            throw new WebApplicationException("Could not parse response from repository.");
        }
        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace("sparql", "http://www.w3.org/2005/sparql-results#");

        $count = (int)$xpath->evaluate("number(//sparql:binding[@name='count']/sparql:literal)");

        return $count;
    }

    /**
     * Execute SPARQL ASK query.
     *
     * @param string $query
     *
     * @return boolean
     */
    public function ask(Query $query)
    {
        $numOfMatches = preg_match('/(?<query>ASK([^}]+)})/ims', $query);
        if($numOfMatches === 0) {
            throw new WebApplicationException("Could not find ASK statement in SPARQL query: '" . str_ireplace("\n", "\\n", $query));
        }

        $response = $this->_query($query, 'query', 'GET', ContentType::APPLICATION_SPARQL_XML);

        if(empty($response)) {
            throw new WebApplicationException("Invalid endpoint response.");
        }

        try {
            $xmlObject = new \SimpleXMLElement($response);
        } catch(\Exception $e) {
            throw new WebApplicationException($e->getMessage());
        }

        if(!isset($xmlObject->boolean)) {
            throw new WebApplicationException("Could not interpret response.");
        }

        return ((string)$xmlObject->boolean === "true");
    }

    /**
     * Query the repository.
     *
     * @param Graphity\Sparql\Query $query
     * @param string $action Action name
     */
    protected function _query(Query $query, $action, $method, $accept)
    {
        $preparedQuery = (string)$query;

        if(strlen($preparedQuery) > 2000) {
            /* some SPARQL endpoints cannot process big queries passed
               through GET. */
            $method = 'POST';
        }

        $client = $this->getClient()
            ->reset()
            ->setPath('/' . $this->getRepositoryName() . '/' . $this->getActionPath($action))
            ->setMethod($method)
            ->setHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8")
            ->setHeader("Accept", $accept)
            ->setData(array('query' => $preparedQuery));

        if(strtoupper($method) === 'POST') {
            $client->setData("query=" . urlencode($preparedQuery));
        }

        list($responseCode, $body, $headers) = $client->executeRequest();

        if(!in_array($responseCode, array(200, 201, 204))) {
            throw new WebApplicationException("Could not retrieve data from repository (status code: {$responseCode}).");
        }

        return $body;
    }

    /**
     * @return Graphity\Repository\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return $this->repositoryName;
    }

    /**
     * Return action path.
     * 
     * Will default to 'sparql' if action not found.
     *
     * @param string $action
     *
     * @return string
     */
    protected function getActionPath($action)
    {
        return array_key_exists($action, $this->actionPaths) ? $this->actionPaths[$action] : 'sparql';
    }
}

