<?php

/*
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Graphity\Util;

/**
 * Basic URI parser object.
 *
 * @group util
 */
final class PhutilURI
{

    private $protocol;

    private $domain;

    private $port;

    private $path;

    private $query = array();

    private $fragment;

    public function __construct($uri)
    {
        $parts = $this->parseURI($uri);
        if($parts) {
            $this->protocol = $parts[1];
            $this->domain = $parts[2];
            $this->port = $parts[3];
            $this->path = $parts[4];
            parse_str($parts[5], $this->query);
            $this->fragment = $parts[6];
        }
    }

    private static function parseURI($uri)
    {
        // NOTE: We allow "+" in the protocol for "svn+ssh" and similar.
        $protocol = '([\w+]+):\/\/';
        $domain = '([a-zA-Z0-9\.\-_]*)';
        $port = '(?::(\d+))?';
        $path = '((?:\/|^)[^#?]*)?';
        $query = '(?:\?([^#]*))?';
        $anchor = '(?:#(.*))?';
        
        $regexp = '/^(?:' . $protocol . $domain . $port . ')?' . $path . $query . $anchor . '$/S';
        
        $matches = null;
        $ok = preg_match($regexp, $uri, $matches);
        if($ok) {
            return array_pad($matches, 7, null);
        }
        
        return null;
    }

    public function __toString()
    {
        $prefix = null;
        if($this->protocol || $this->domain || $this->port) {
            $protocol = nonempty($this->protocol, 'http');
            $prefix = $protocol . '://' . $this->domain;
            if($this->port) {
                $prefix .= ':' . $this->port;
            }
        }
        
        if($this->query) {
            $query = '?' . http_build_query($this->query);
        } else {
            $query = null;
        }
        
        if(strlen($this->getFragment())) {
            $fragment = '#' . $this->getFragment();
        } else {
            $fragment = null;
        }
        
        return $prefix . $this->getPath() . $query . $fragment;
    }

    public function setQueryParam($key, $value)
    {
        if($value === null) {
            unset($this->query[$key]);
        } else {
            $this->query[$key] = $value;
        }
        return $this;
    }

    public function setQueryParams(array $params)
    {
        $this->query = $params;
        return $this;
    }

    public function getQueryParams()
    {
        return $this->query;
    }

    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function alter($key, $value)
    {
        $altered = clone $this;
        $altered->setQueryParam($key, $value);
        return $altered;
    }

}

