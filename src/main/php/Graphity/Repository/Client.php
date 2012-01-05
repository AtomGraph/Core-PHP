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

use Graphity\WebApplicationException;

class Client
{
    /**
     * Remote host did not respond with valid HTTP response
     * or timed out.
     */
    const ERROR_MALFORMED_RESPONSE = 1000;

    /**
     * @var string $endpointUrl
     */
    private $endpointUrl = null;

    /**
     * @var resource $connection CURL connection.
     */
    private $connection = null;

    /**
     * @var string $method
     */
    private $method = 'GET';

    /**
     * @var string $path
     */
    private $path = null;

    /**
     * @var mixed $data
     */
    private $data = null;

    /**
     * @var array $headers
     */
    private $headers = array();

    /**
     * Initialize client variables.
     *
     * @param string $endpointUrl
     */
    public function __construct($endpointUrl)
    {
        $this->endpointUrl = $endpointUrl;
        $this->connection = curl_init();
    }

    /**
     * Destroys the curl connection.
     */
    public function __destruct()
    {
        curl_close($this->connection);
    }

    /**
     * Execute request on SPARQL endpoint.
     */
    public function executeRequest()
    {
        curl_setopt($this->connection, CURLOPT_URL, $this->getURL());
        curl_setopt($this->connection, CURLOPT_CUSTOMREQUEST, $this->getMethod());
        curl_setopt($this->connection, CURLOPT_HTTPHEADER, $this->prepareHTTPHeaders());
        curl_setopt($this->connection, CURLOPT_HEADER, true); // return response headers
        curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->connection, CURLOPT_FRESH_CONNECT, true);

        if($this->getMethod() === "POST") {
            curl_setopt($this->connection, CURLOPT_POSTFIELDS, $this->getData());
        }

        $response = curl_exec($this->connection);

        return $this->parseHTTPResponse($response);
    }

    /**
     * Reset connection for next request.
     */
    public function reset()
    {
        $this->method = 'GET';
        $this->path = null;
        $this->data = null;
        $this->headers = array();

        return $this;
    }

    /**
     * Prepare request URL.
     *
     * @return string
     */
    protected function getURL()
    {
        $url = rtrim($this->endpointUrl, "/") . "/" . ltrim($this->getPath(), "/");

        if($this->getMethod() === "GET") {
            $data = $this->getData();

            // if data is array, prepare a string
            if(is_array($data)) {
                $listOfParts = array();
                foreach($data as $key => $value) {
                    $listOfParts[] = urlencode($key) . "=" . urlencode($value);
                }
                $data = implode("&", $listOfParts);
            }

            // if data is a string - add it at the end of url.
            if(is_string($data) && !empty($data)) {
                if(!strpos($url, "?")) {
                    $url .= "?" . $data;
                } else {
                    $url .= "&" . $data;
                }
            }
        }

        return $url;
    }

    /**
     * Parse HTTP response and return array with:
     *      - response code
     *      - response body
     *      - response headers
     *
     * Method is from libphutils' HTTPFuture,
     * @see https://github.com/facebook/libphutil/blob/master/src/future/http/HTTPFuture.php
     *
     * @param string $response
     *
     * @return array
     */
    protected function parseHTTPResponse($response)
    {
        static $rex_base = "@^(?<head>.*?)\r?\n\r?\n(?<body>.*)$@s";
        static $rex_head = "@^HTTP/\S+ (?<code>\d+) .*?(?:\r?\n(?<headers>.*))?$@s";
        static $rex_header = '@^(?<name>.*?):\s*(?<value>.*)$@';

        static $malformed = array(self::ERROR_MALFORMED_RESPONSE, null, array());

        // remove "HTTP/1.1 100 Continue"
        $response = ltrim(str_ireplace("HTTP/1.1 100 Continue", "", $response), "\r\n ");

        $matches = null;
        if(! preg_match($rex_base, $response, $matches)) {
            return $malformed;
        }

        $head = $matches['head'];
        $body = $matches['body'];

        if(! preg_match($rex_head, $head, $matches)) {
            return $malformed;
        }

        $response_code = (int)$matches['code'];

        $headers = array();
        if(isset($matches['headers'])) {
            $head_raw = $matches['headers'];
            if(strlen($head_raw)) {
                $headers_raw = preg_split("/\r?\n/", $head_raw);
                foreach($headers_raw as $header) {
                    $m = null;
                    if(preg_match($rex_header, $header, $m)) {
                        $headers[] = array($m['name'], $m['value']);
                    } else {
                        $headers[] = array($header, null);
                    }
                }
            }
        }

        return array($response_code, $body, $headers);
    }

    /**
     * Prepares HTTP headers.
     *
     * @return string
     */
    public function prepareHTTPHeaders()
    {
        $headers = array();
        foreach($this->getAllHeaders() as $name => $value) {
            $headers[] = "{$name}: {$value}";
        }

        return $headers;
    }

    /**
     * Returns SPARQL endpoint URL.
     *
     * @return string
     */
    public function getEndpointUrl()
    {
        return $this->endpointUrl;
    }

    /**
     * Set URI path component.
     *
     * @param string $path      URI path component
     *
     * @return Graphity\Repository\Client
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Return URI path component.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set request data
     *
     * @param mixed $data       Request data
     *
     * @return Graphity\Repository\Client
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Return request data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set HTTP request method.
     *
     * @param string $method        HTTP request method
     *
     * @return Graphity\Repository\Client
     */
    public function setMethod($method)
    {
        if(!in_array(strtoupper($method), array("GET", "POST", "PUT", "DELETE"))) {
            throw new WebApplicationException("HTTP method can be one of: 'GET', 'POST', 'PUT' or 'DELETE'.");
        }

        $this->method = $method;
        return $this;
    }

    /**
     * Get HTTP request method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set HTTP header.
     *
     * @param string $name      Header name
     * @param string $value     Header value
     *
     * @return Graphity\Repository\Client
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Return a HTTP header value.
     *
     * @param string $name      Header name
     *
     * @return string
     */
    public function getHeader($name)
    {
        return array_key_exists($name, $this->headers) ? $this->headers[$name] : null;
    }

    /**
     * Return array with all HTTP headers.
     *
     * @return array
     */
    public function getAllHeaders()
    {
        return $this->headers;
    }
}

