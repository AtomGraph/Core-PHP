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

/**
 * Provides access to HTTP response, such as status, headers etc.
 * Wraps PHP's functions and global arrays to emulate Java's HttpServletResponse interface. 
 * That means it can only be used to represent the "current" response.
 */
class Response implements ResponseInterface
{

    private $cookies = array();

    private $headers = array();

    private $buffer = "";

    private $writer = null;

    private $status = null;

    private $contentType = null;

    private $encoding = null;


    /**
     *  Constructs new response.
     */

    public function __construct()
    {
        $this->writer = fopen("php://output", "w");
    }

    /**
     *  Adds the specified cookie to the response.
     *
     *  @param Cookie $cookie Cookie to return to the client
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
     *  Get list of all cookies
     *
     *  @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     *  Sets a HTTP response header.
     *
     *  @param string $name Name of the header
     *  @param string $value Value of the header
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }
    
    /**
     *  Return a list of HTTP headers.
     *
     *  @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     *  Returns response status.
     *
     *  @return int Status code
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *  Sets response status.
     *
     *  @param int $status Status code
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *  Returns content type.
     *  
     *  @return string Content type
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     *  Sets content type (e.g. "text/html").
     *
     *  @param string $contentType Content type
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     *  Returns character encoding.
     *
     *  @return string Character encoding
     */
    public function getCharacterEncoding()
    {
        return $this->encoding;
    }

    /**
     *  Sets character encoding (e.g. "UTF-8").
     *
     *  @param string $encoding Character encoding
     */
    public function setCharacterEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     *  Sends a temporary redirect response to the client using the specified redirect location URL.
     *
     *  @param string $url Redirect location URL
     */
    public function sendRedirect($uri)
    {
        header("Cache-Control: no-cache, max-age=0", true);
        header("Location: " . $uri);
        exit();
    }

    /**
     *  Writes a string into the response output buffer.
     *
     *  @param string $string String
     */
    /*
    public function write($string)
    {
        $this->buffer .= $string;
    }
    */

    /**
     *  Returns the whole response output buffer.
     *
     *  @return string Buffer string
     */
     /*
    public function getBuffer()
    {
        return $this->buffer;
    }
    */

    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Sets HTTP response headers.
     *
     * @param Response $response Response to write out and to send to the client
     */
    public final function commit()
    {
        header("HTTP/1.1 " . (string)$this->getStatus());
        if($this->getContentType() != null) {
            if($this->getCharacterEncoding() != null) {
                header("Content-Type: " . $this->getContentType() . "; charset=" . $this->getCharacterEncoding());
            } else {
                header("Content-Type: " . $this->getContentType());
            }
        }
        
        foreach($this->getHeaders() as $name => $value) {
            header($name . ": " . $value, true);
        }
        
        if($this->getBuffer() !== null) {
            header(sprintf("Content-Length: %d", mb_strlen($this->getBuffer())));
            echo $this->getBuffer();
        }
    }

}

