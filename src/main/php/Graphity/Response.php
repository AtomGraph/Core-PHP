<?php
/**
 * Provides access to HTTP response, such as status, headers etc.
 * Wraps PHP's functions and global arrays to emulate Java's HttpServletResponse interface. That means it can only be used to represent the "current" response.
 *
 * @package		graphity
 * @author		Martynas Jusevicius <pumba@xml.lt>
 * @link		http://www.xml.lt
 */

namespace Graphity;

class Response implements ResponseInterface
{

    private $cookies = array();

    private $headers = array();

    private $buffer = "";

    private $status = null;

    private $contentType = null;

    private $encoding = null;

    /**
     * Adds the specified cookie to the response.
     * @param Cookie $cookie Cookie to return to the client
     */
    public function addCookie(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
        
        if($cookie->getPath() != null)
            setcookie($cookie->getName(), $cookie->getValue(), time() + $cookie->getMaxAge(), $cookie->getPath()); // QUIRK
        else
            setcookie($cookie->getName(), $cookie->getValue(), time() + $cookie->getMaxAge());
    }

    /**
     * Sets a HTTP response header.
     * @param string $name Name of the header
     * @param string $value Value of the header
     */
    
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }
    
    /**
     * Return a list of HTTP headers.
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns response status.
     * @return int Status code
     */
    
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets response status.
     * @param int $status Status code
     */
    
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns content type.
     * @return string Content type
     */
    
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets content type (e.g. "text/html").
     * @param string $contentType Content type
     */
    
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    
     //$this->setHeader("Content-type", $outputType);
    }

    /**
     * Returns character encoding.
     * @return string Character encoding
     */
    
    public function getCharacterEncoding()
    {
        return $this->encoding;
    }

    /**
     * Sets character encoding (e.g. "UTF-8").
     * @param string $encoding Character encoding
     */
    
    public function setCharacterEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * Sends a temporary redirect response to the client using the specified redirect location URL.
     * @param string $url Redirect location URL
     */
    
    public function sendRedirect($uri)
    {
        header("Cache-Control: no-cache, max-age=0", true);
        header("Location: " . $uri);
        exit();
    }

    /**
     * Writes a string into the response output buffer.
     * @param string $string String
     */
    
    public function write($string)
    {
        $this->buffer .= $string;
    }

    /**
     * Returns the whole response output buffer.
     * @return string Buffer string
     */
    
    public function getBuffer()
    {
        return $this->buffer;
    }
}

