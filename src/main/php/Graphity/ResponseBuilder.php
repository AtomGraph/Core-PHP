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
 *  @author         Julius Šėporaitis <julius@graphity.org>
 *  @link           http://graphity.org/
 */

namespace Graphity;

use Graphity\Cookie;
use Graphity\Response;

/**
 *  A class used to build Response instances that contain metadata instead of or 
 *  in addition to an entity. An initial instance may be obtained via static
 *  methods of the Response class, instance methods provide the ability to set
 *  metadata.
 *
 *  Interface based on JAX-RS: 
 *      http://docs.jboss.org/resteasy/docs/1.0.0.GA/javadocs/javax/ws/rs/core/Response.ResponseBuilder.html
 *
 *  @note We do not support expires() and ("Expires") header in favor of 
 *      cacheControl() ("Cache-Control"), because it is cleaner way to work 
 *      with browser/proxy cache. Though, feel free to create a feature
 *      request.
 */
class ResponseBuilder {

    /**
     *  @var Graphity\Resource
     */
    protected $response = null;

    protected function __construct() {
        $this->response = new Response();
    }

    /**
     *  Create a response instance from the current ResponseBuilder.
     *
     *  @return Graphity\Response
     */
    public function build() {
        return $this->response;
    }

    /**
     *  Set the cache control data on the ResponseBuilder.
     *
     *  @param string $header
     *  
     *  @return Graphity\ResponseBuilder
     */
    public function cacheControl($header) {
        $this->response->setHeader("Cache-Control", $header);

        return $this;
    }

    /**
     *  Set the content location on the ResponseBuilder.
     *
     *  @param string $location
     *
     *  @return Graphity\ResponseBuilder
     */
    public function contentLocation($location) {
        $this->response->setHeader("Content-Location", $location);

        return $this;
    }

    /**
     *  Add cookie to the ResponseBuilder.
     *
     *  @param Cookie $cookie
     *
     *  @return Graphity\ResponseBuilder
     */
    public function cookie(Cookie $cookie) {
        $this->response->addCookie($cookie);

        return $this;
    }

    /**
     *  Set the entity (body) on the ResponseBuilder.
     *
     *  @param string $body
     *
     *  @return Graphity\ResponseBuilder
     */
    public function entity($body) {
        fwrite($this->response->getWriter(), $body);

        return $this;
    }

    /**
     *  Add a header to the ResponseBuilder.
     *
     *  @param string $name
     *  @param string $value
     *
     *  @return Graphity\Response
     */ 
    public function header($name, $value) {
        if(strtolower($name) === "content-type") {
            $this->type($value);
        } else if(strtolower($name) === "last-modified") {
            $this->lastModified($value);
        } else {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    /**
     *  Set the language (locale) on the ResponseBuilder.
     *
     *  @param string $locale
     *
     *  @return Graphity\ResponseBuilder
     */
    public function language($locale) {
        $this->response->setHeader("Content-Language", $locale);

        return $this;
    }

    /**
     *  Set the last modified date on the ResponseBuilder.
     *
     *  @param \DateTime|string $lastModified
     *
     *  @return Graphity\ResponseBuilder
     */
    public function lastModified($lastModified) {
        if($lastModified instanceof \DateTime) {
            // otherwise consider it is already formatted string representation
            $lastModified->setTimezone(new \DateTimeZone('GMT'));
            $lastModified = $lastModified->format("D, d M Y H:i:s") . " GMT"; 
        }
        $this->response->setHeader("Last-Modified", $lastModified);

        return $this;
    }

    /**
     *  Set the location on the ResponseBuilder.
     *
     *  @param string $location
     *
     *  @return Graphity\ResponseBuilder
     */
    public function location($location) {
        $this->response->setHeader("Location", $location);

        return $this;
    }

    /**
     *  Create a new builder instance
     *
     *  @return Graphity\ResponseBuilder
     */
    public static function newInstance() {
        $className = get_called_class();
        return new $className;
    }

    /**
     *  Set the status on the ResponseBuilder.
     *
     *  @param integer $status
     *
     *  @return Graphity\ResponseBuilder
     */
    public function status($status) {
        $this->response->setStatus($status);

        return $this;
    }

    /**
     *  Set the response media type on this ResponseBuilder.
     *
     *  @param string $mimeType
     *
     *  @return Graphity\ResponseBuilder
     */
    public function type($mimeType) {
        $this->response->setContentType($mimeType);

        return $this;
    }

    /**
     *  Set the representation metadata on the ResponseBuilder.
     *
     *  @param string $variant
     *
     *  @return Graphity\ResponseBuilder
     */
    public function variant($variant) {
        $this->response->setHeader("Vary", $variant);

        return $this;
    }

    /**
     *  Add a Vary header that lists the available variants.
     *
     *  @param array $variants
     *
     *  @return Graphity\ResponseBuilder
     */
    public function variants(array $variants) {
        return $this->response->variant(implode(",", $variants));
    }
}

