<?php
/**
 * An abstract HTTP Resource for subclassing. It should be specified as a base class in Propel schema.
 * If used without Propel, it should not extend BaseObject.
 * 
 * @package		graphity
 * @author		Martynas Jusevicius <pumba@xml.lt>
 * @link		http://www.xml.lt
 */

namespace Graphity;

abstract class Resource implements ResourceInterface
{
    /**
     * @var string
     */
    private $scheme = "http";

    /**
     * @var string
     */
    private $baseUri = null;

    /**
     * @var string
     */
    private $uri = null;

    /**
     * @var Resource
     */
    private $resource = null;

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
        if(!empty($_SERVER['HTTPS'])) {
            // make sure it's not IIS.
            if($_SERVER['HTTPS'] !== "off") {
                $this->scheme = "https";
            }
        }
        $this->request = $request;
        $this->router = $router;
        $this->baseUri = $this->scheme . "://" . $request->getServerName() . "/"; // request host becomes mapping host
        $this->path = $this->extractPath();
        //$host = rtrim($request->getHeader("HTTP_HOST"), "/");
        $this->uri = $this->baseUri . rtrim($this->getPath(), "/");
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
        return $this->uri;
    }

    public function setURI($uri)
    {
        $this->uri = $uri;
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
     * Extracts and returns the full URI (including both host and resource URIs) of the current Request, by striping the query string.
     *
     * @param Request $request Request to extract the URI from
     *
     * @return string URI
     */
    private function extractPath()
    {
        $path = $this->getRequest()->getRequestURI();
        
        if(($pos = strpos($path, "?")) !== false) {
            $path = substr($path, 0, $pos); // strip the query string
        }

        return trim($path, "/");
    }

    /**
     * Extracts and returns the absolute path of the current Request, by striping the query string.
     *
     * @param Request $request Request to extract the path from
     *
     * @return string path
     */
    public function getPath()
    {
        return $this->path;
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
        try {
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
        }
        catch(Exception $e) {
            $this->log($e);
            $this->setResponse(new ExceptionView($e, $this->getRequest()));
            $this->getResponse()->display();
        }
        $this->output();  
    }

    protected function authorize() {
        return true;
    }

}
