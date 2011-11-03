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

namespace Graphity\Util;

/**
 * Uri builder class for easier composition of URIs.
 * 
 * Example usage:
 * 
 * UriBuilder::newInstance()->host("www.seporaitis.net")->path("index.php/2010/01/20/sis-zurnalas-uzsidaro/")->fragment("comments").build();
 * 
 * Will return:
 * 
 * "http://www.seporaitis.net/index.php/2010/01/20/sis-zurnalas-uzsidaro/#comments"
 * 
 * Based on this interface: http://jsr311.java.net/nonav/javadoc/javax/ws/rs/core/UriBuilder.html
 *  
 * Note that this implementation ignores regular expressions inside { }.
 */
class UriBuilder {
    
    const _REGEXP_SCHEMA = '/(\w+):\/\//i';
    const _REGEXP_PORT = '/:(\d+)[\/]?/i';
    const _REGEXP_HOST = '/\w+:\/\/([\w\.\-\d]+)/i';
    
    /**
     * @var string
     */
    protected $scheme = null;
    
    /**
     * @var string
     */
    protected $hostname = null;
    
    /**
     * @var int
     */
    protected $port = null;
    
    /**
     * @var array
     */
    protected $listOfSegments = array();
    
    /**
     * @var array
     */
    protected $listOfQueryParams = array();
    
    /**
     * @var string
     */
    protected $fragment = null;
    
    public function __construct() {
        // empty
    }
    
    /**
     * Create a new instance
     * 
     * @return UriBuilder
     */
    public static function newInstance() {
        $className = get_called_class();
        return new $className;
    }
    
    /**
     * Create a new instance from an existing URI.
     * 
     * @param mixed $uri
     * 
     * @return UriBuilder
     */
    public static function fromUri($uri) {
        if(is_string($uri)) {
            $uri = new PhutilURI($uri);
        }
        
        $className = get_called_class();
        $builder = new $className;
        
        $builder->scheme($uri->getProtocol());
        $builder->host($uri->getDomain());
        $builder->port($uri->getPort() == 80 ? null : $uri->getPort());
        
        $builder->path($uri->getPath());
        
        foreach($uri->getQueryParams() as $name => $value) {
            $builder->queryParam($name, $value);
        }
        
        $builder->fragment($uri->getFragment());
        
        return $builder;
    }
    
    /**
     * Create an UriBuilder representing relative URI initialized from URI path.
     * 
     * @param string $path
     * 
     * @return UriBuilder
     */
    public static function fromPath($path) {
        $className = get_called_class();
        $builder = new $className;
        
        $partMap = parse_url($path);
        
        $builder->listOfSegments = explode("/", $partMap['path']);
        if(array_key_exists('fragment', $partMap)) {
            $builder->fragment($partMap['fragment']);
        }
        
        if(array_key_exists('query', $partMap)) {
            $nvPairs = explode("&", $partMap['query']);
            foreach($nvPairs as $pair) {
                $parts = explode("=", $pair);
                $builder->queryParam($parts[0], $parts[1]);
            }
        }
        
        return $builder;
    }
    
    /**
     * @param string $value
     * 
     * @return UriBuilder
     */
    public function scheme($value) {
        $this->scheme = $value;
        return $this;
    }
    
    /**
     * @param string $value
     * 
     * @return UriBuilder
     */
    public function host($value) {
        $partMap = parse_url($value);
        if(array_key_exists('host', $partMap)) {
            $this->hostname = $partMap['host'];
        }
        if(array_key_exists('path', $partMap)) {
            $host = $partMap['path'];
            if(($slashPos = strpos($host, "/")) !== false) {
                $host = substr($host, 0, $slashPos);
            }
            $this->hostname = $host;
        }
        
        return $this;
    }
    
    /**
     * @param int $value
     * 
     * @return UriBuilder
     */
    public function port($value) {
        $this->port = $value;
        return $this;
    }
    
    /**
     * @param string $value
     * 
     * @return UriBuilder
     */
    public function path($value) {
        $list = explode("/", $value);
        foreach($list as $segment) {
            $this->segment($segment);
        }
        
        return $this;
    }

    /**
     * @param string $value
     * 
     * @return UriBuilder
     */
    public function replacePath($value) {
        $this->listOfSegments = array();

        $this->path($value);

        return $this;
    }

    /**
     * Append a single path segment
     * 
     * @param string $value
     * 
     * @return UriBuilder
     */
    public function segment($value) {
        if(empty($value)) {
            return $this;
        }
        
        $this->listOfSegments[] = $value;
        return $this;
    }
    
    /**
     * Add a query name=value pair
     * 
     * @param string $name
     * @param string $value
     * 
     * @return UriBuilder
     */
    public function queryParam($name, $value) {
        $this->listOfQueryParams[$name] = $value;
        return $this;
    }
    
    /**
     * @param string $value
     * 
     * @return UriBuilder
     */
    public function fragment($value) {
        $this->fragment = $value;
        return $this;
    }
    
    /**
     * Build URI from created template
     * 
     * Example usage:
     * 
     * UriBuilder::newInstance()->host("coolwebsite.com")->path("/{lang}/news/{id}")->build("en", 1234);
     * 
     * Will return
     * 
     * "http://coolwebsite.com/en/news/1234"
     * 
     * @param mixed $param1, $param2, ...
     *  
     * @returns string
     */
    public function build() {
        
        $paramMap = func_get_args();
        
        return $this->buildWithTemplate(function($value) use (&$paramMap) {
            $startPos = strpos($value, "{");
            if($startPos === false) {
                return $value;
            }
            
            $endPos = strrpos($value, "}");
            if($endPos === false) {
                return $value;
            }
            
            $key = substr($value, $startPos + 1, $endPos - 1);
            if(strpos($key, ":") !== false) {
                $key = substr($key, 0, strpos($key, ":"));
            }
            
            $newValue = array_shift($paramMap);
            if($newValue === null) {
                return $value;
            }
            
            return str_ireplace("{" . $key . "}", $newValue, $value);
        });
    }
    
    /**
     * Build URI from created template using parameter map.
     * 
     * Example usage:
     * 
     * UriBuilder::newInstance()->host("coolwebsite.com")->path("/{lang}/news/{id}")->buildFromMap(array('lang' => "en", 'id' => 1234));
     * 
     * Will return
     * 
     * "http://coolwebsite.com/en/news/1234"
     * 
     * @param array $paramMap
     * @returns string
     */
    public function buildFromMap(array $paramMap = array()) {
        return $this->buildWithTemplate(function($value) use ($paramMap) {
            if(strpos($value, "{") === false) {
                return $value;
            }
            
            foreach($paramMap as $key => $newValue) {
                $value = str_ireplace("{" . $key . "}", $newValue, $value);
            }
            
            return $value;
        });
    }
    
    /**
     * Actual Uri builder.
     * 
     * Takes an anonymous function $template and applies it to all parts of
     * the uri, before returning it.
     * 
     * @param function $template
     * 
     * @return string
     */
    protected function buildWithTemplate($template) {
        $uri = "";
        
        if($this->scheme !== null) {
            $uri .= $this->scheme . "://";
        }
        
        if($this->hostname !== null) {
            if(strlen($uri) === 0) {
                $uri .= "http://";
            }
            $uri .= $template($this->hostname);
        }
        
        if(!empty($this->port)) {
            $uri .= ":" . $this->port;
        }
        
        if(strlen($uri) > 0) {
            if(array_key_exists(0, $this->listOfSegments)) {
                if(!empty($this->listOfSegments[0])) {
                    $uri .= "/";
                } else {
                    array_shift($this->listOfSegments);
                }
            } else {
                $uri .= "/";
            }
        }
        
        foreach($this->listOfSegments as $key => $segment) {
            $uri .= urlencode($template($segment)) . (($key < count($this->listOfSegments) - 1) ? "/" : "");
        }
        
        if(count($this->listOfQueryParams) > 0) {
            $uri .= "?";
            $counter = 0;
            foreach($this->listOfQueryParams as $name => $value) {
                $uri .= urlencode($template($name)) . "=" . urlencode($template($value)) . (($counter < (count($this->listOfQueryParams) - 1)) ? "&" : "");
                $counter++;
            }
        }
        
        if($this->fragment !== null) {
            $uri .= "#" . urlencode($template($this->fragment));
        }
        
        return $uri;
    }
}
