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
 *  @author         Martynas Jusevičius <pumba@xml.lt>
 *  @link           http://graphity.org/
 */

namespace Graphity;

use Graphity\Util\UriBuilder;
use Graphity\Exception;

/**
 * An abstract HTTP Resource for subclassing. It should be specified as a base class in Propel schema.
 * If used without Propel, it should not extend BaseObject.
 */
abstract class Resource implements ResourceInterface
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
    private function setResponse(Response $response)
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
     * @param string $uri
     * 
     * @return boolean
     */
    abstract public function exists();

    /**
     * Sets HTTP response headers and writes out the buffer.
     *
     * @param Response $response Response to write out and to send to the client
     */
    protected final function output()
    {
        header("HTTP/1.1 " . (string)$this->getResponse()->getStatus());
        if($this->getResponse()->getContentType() != null) {
            if($this->getResponse()->getCharacterEncoding() != null) {
                header("Content-Type: " . $this->getResponse()->getContentType() . "; charset=" . $this->getResponse()->getCharacterEncoding());
            } else {
                header("Content-Type: " . $this->getResponse()->getContentType());
            }
        }
        
        foreach($this->getResponse()->getHeaders() as $name => $value) {
            header($name . ": " . $value, true);
        }
        
        if($this->getResponse()->getBuffer() !== null) {
            header(sprintf("Content-Length: %d", mb_strlen($this->getResponse()->getBuffer())));
            echo $this->getResponse()->getBuffer();
        }
    }

    /**
     * Processes the HTTP Request. Finds an appropriate Resource, passes the control to it, and displays the resulting View.
     */
    public final function process()
    {
        //try {
            $ref = new \ReflectionAnnotatedClass($this);
            if($ref->hasAnnotation('Singleton') === false && $this->exists() === false) {
                throw new WebApplicationException(Response::SC_NOT_FOUND, "Resource not found");
            }
            if (!$this->authorize()) {
                throw new WebApplicationException(Response::SC_FORBIDDEN, "Access denied");
            }

            $methodName = $this->getRouter()->matchMethod($this);
            if($methodName === null) {
                $this->getResponse()->setStatus(Response::SC_METHOD_NOT_ALLOWED);
            } else {
                $this->setResponse($this->$methodName());
                if($this->getResponse() instanceof View)
                    $this->getResponse()->display();
            }
        //}
        // looks like we shouldn't catch Exceptions here in the parent class?
        /*
        catch(Exception $e) {
            $this->log($e);
            $this->setResponse(new ExceptionView($e, $this->getRequest())); // ExceptionView not known at this point!
            $this->getResponse()->display();
        }
        */
        $this->output();  
    }

    protected function authorize() {
        return true;
    }

    protected abstract function describe(); // move to ResourceInterface if this works OK

}
