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
 *  @author         Martynas Jusevičius <martynas@graphity.org>
 *  @link           http://graphity.org/
 */

namespace Graphity;

use Graphity\Util\UriBuilder;
use Graphity\Exception;
use Graphity\WebApplicationException;
use Graphity\Rdf as Rdf;
use Graphity\Sparql as Sparql;

/**
 * RESTful RDF Resource for subclassing.
 */
class Resource implements ResourceInterface
{
    /**
     * @var string
     */
    private $baseUri = null;

    /**
     * @var Response
     */
    private $response = null;

    /**
     * @var Request
     */
    private $request = null;

    /**
     * @var Router
     */
    private $router = null;

    /**
     * Constructs a new Controller.
     */
    public function __construct(Request $request, Router $router)
    {
        $this->request = $request;
        $this->router = $router;
        $this->baseUri = UriBuilder::newInstance()->
            scheme($request->getScheme())->
            host($request->getServerName())->
            port($request->getServerPort())->
            build();

        $this->response = new Response();
        $this->response->setStatus(Response::SC_OK);
        $this->response->setCharacterEncoding("UTF-8");
    }

    /**
     * Returns full URI of the Resource.
     * 
     * @return string Resource URI
     */
    public function getURI()
    {
        $uri = rtrim($this->getBaseURI(), "/") . "/" . trim($this->getPath(), "/");
        if ($uri == $this->getBaseURI()) return $uri;
        else return rtrim($uri, "/");
    }

    /**
     * Initializes Request object from $_GET, $_POST, and $_SERVER.
     *
     * @return Request request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Extracts and returns the absolute path of the current Request, striping the query string.
     *
     * @param Request $request Request to extract the path from
     *
     * @return string path
     */
    public function getAbsolutePath()
    {
        $absolutePath = UriBuilder::fromPath($this->getRequest()->getPathInfo())->
            build();

        return rtrim($absolutePath, "/");
    }

    /**
     * Extracts and returns the path of the current Request relative to the base URI, striping the query string.
     *
     * @param Request $request Request to extract the path from
     *
     * @return string path
     */
    public function getPath()
    {
         return substr($this->getAbsolutePath(), strlen(parse_url($this->getBaseURI(), PHP_URL_PATH)));
    }

    /**
     * Return response instance.
     * 
     * @return Respones
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response instance
     * 
     * @param Response $response
     */
    protected function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getBaseURI()
    {
        return $this->baseUri;
    }

    protected final function setBaseURI($baseUri)
    {
        $this->baseUri = $baseUri;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function log(\Exception $e)
    {
       if($e instanceof WebApplicationException) {
           error_log(sprintf("[%d] %s\n%s", $e->getCode(), $e->getMessage(), $e->getTraceAsString())); 
       } else {
           error_log(sprintf("%s\n%s", $e->getMessage(), $e->getTraceAsString()));
       }
    }

    /**
     * Check if resource exists.
     * 
     * @return boolean
     */
    public function exists() {
        return true;
    }

    /**
     * Check if agent has access to resource.
     * 
     * @return boolean
     */
    public function authorize() {
        return true;
    }

    /**
     * Returns DOM description of the resource (usually as RDF/XML).
     * 
     * @return DOMDocument
     */
    public function describe() {
        $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "webapp" . DIRECTORY_SEPARATOR . "WEB-INF" . DIRECTORY_SEPARATOR . "sparql" . DIRECTORY_SEPARATOR . "describe.rq";

        $queryString = file_get_contents($fileName);

        // TO-DO! Repository classes still not in Graphity
        // TO-DO! Should return DOMDocument, not a string
        return $this->getRepository()->ask(Sparql\Query::newInstance()
            ->setQuery($queryString)
            ->setVariable('uri', new Rdf\Resource($this->getURI())));
    }

}
