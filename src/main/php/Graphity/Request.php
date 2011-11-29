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
 * Provides HTTP request information such as headers, query parameters etc.
 * Wraps PHP's functions and global arrays to emulate Java's HttpServletRequest interface. 
 * That means it can only be used to represent the "current" request.
 */
class Request implements RequestInterface
{
    private $attributes = array();

    private $session = null;
       
    /**
     * Returns a request attribute, or null if it does not exist. They are used to save and share data between components in a context of a single request.
     * @param string $name Name of the attribute
     * @return mixed Attribute
     */
    
    public function getAttribute($name)
    {
        if(isset($this->attributes[$name]))
            return $this->attributes[$name];
        else
            return null;
    }

    /**
     * Sets a request attribute. They are used to save and share data between components in a context of a single request.
     * @param string $name Name of the attribute
     * @param string $value Value of the attribute
     */
    
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function getContentType()
    {
        return $this->getHeader("CONTENT_TYPE");
    }

    public function getContentLength()
    {
        return $this->getHeader("CONTENT_LENGTH");
    }

    public function addCookie(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
    }

    public function getCookies()
    {
        $cookies = array();

        foreach($_COOKIE as $name => $value)
          $cookies[] = new Cookie($name, $value);

        if (count($cookies) > 0)
            return $cookies;
        else
            return null;
    }

    /**
     * Returns a HTTP request parameter (from both GET and POST) as a string, or null if it does not exist.
     * @param string $name Name of the parameter
     * @return string Value of the parameter
     */
    
    public function getParameter($name)
    {
        if (isset($_GET[$name]))
            return $_GET[$name];

        if (isset($_POST[$name]))
            return $_POST[$name];

        return null;
    }

    /**
     * Returns a HTTP method (GET/POST/PUT/DELETE etc.) of this request.
     * @return string Request method
     */
    
    public function getMethod()
    {
        /* This is to support some browsers that do not support PUT & DELETE methods,
         * example: IE.
         * 
         * PUT and DELETE should be tunnelled through POST with additional parameter:
         * _method=PUT or _method=DELETE
         */
        if(strtoupper($this->getHeader('REQUEST_METHOD')) === "POST") {
            if(in_array(strtoupper($this->getParameter('_method')), array("PUT", "DELETE"))) {
                return strtoupper($this->getParameter('_method'));
            }
        }
        
        return $this->getHeader("REQUEST_METHOD");
    }

    /**
     * Returns a HTTP request header, or null if it does not exist.
     * Header names are the same as in PHP's $_SERVER[].
     * @param string $name Name of the header
     * @return string Value of the header
     */
    
    public function getHeader($name)
    {
        if (isset($_SERVER[$name]))
            return $_SERVER[$name];
        else
            return null;
    }

    /**
     * Returns the current Session object associated with this Request.
     * @return Session Session
     */
    
    public function getSession()
    {
        if($this->session == null)
            $this->session = new Session();
        return $this->session;
    }

    /**
     * Returns a map (array with keys) of parameters of this Request.
     * @return array
     */
    
    public function getParameterMap()
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Returns Returns any extra path information associated with the URL the client sent when it made this request. The extra path information follows the servlet path but precedes the query string. This method returns null if there was no extra path information.
     * Same as the value of the CGI variable PATH_INFO.
     * @return string
     */
    
    public function getPathInfo()
    {
        return $this->getHeader("PATH_INFO");
    }

    /**
     * Returns the part of this request's URL from the protocol name up to the query string in the first line of the HTTP request.
     * Same as the value of the CGI variable REQUEST_URI.
     * @return string
     */
    
    public function getRequestURI()
    {
        if($this->getHeader("REQUEST_URI") != null)
            return $this->getHeader("REQUEST_URI");
        else
            return $this->getScheme() . "://" . $this->getServerName() . $this->getPathInfo();
    }

    /**
     * Returns host name of the server.
     * @return string
     */
    
    public function getServerName()
    {
        $port = ($this->getServerPort() == 80 || $this->getServerPort() == null) ? "" : (":" . $this->getServerPort());
        return $this->getHeader("SERVER_NAME") . $port;
    }

    /**
     * Returns port number of the server.
     * @return string
     */
    
    public function getServerPort()
    {
        return $this->getHeader("SERVER_PORT");
    }

    public function getScheme()
    {
        if ($this->getHeader('HTTPS') !== null && $this->getHeader('HTTPS') !== "off")
            return "https";

        return "http";
    }

    public function getQueryString()
    {
		return $_SERVER["QUERY_STRING"];
    }

	public function getInputStream()
	{
		return fopen("php://input", "r");
	}

}

?>
