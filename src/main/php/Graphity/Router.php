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

namespace Graphity;

/**
 * Router class
 *
 * Manages URI matching-to-resource and URI building-from-resource.
 */
class Router
{

    /**
     * @var array
     */
    protected $routes = array();

    /**
     * Constructor
     * 
     * Routing table is a nested array like this:
     * 
     * $routes = array(
     * 'ResourceClassName' => array(
     * 'buildPath' => '/resource/{year}',
     * 'matchPath' => '^/resource/\d{4}$',
     * )
     * );
     * 
     * @param array $routes
     */
    public function __construct(array $routes)
    {
        $this->includeRouteArray($routes);
    }

    public function includeRouteArray(array $routes)
    {
        $this->routes = array_merge($this->routes, $routes);
    }

    public function importRouteArray(array $routes)
    {
        throw new WebApplicationException("Could not import route array.");
        //$this->routes = array_merge($this->routes, $routes);
    }

    /**
     * Matches URI to classname.
     * 
     * If classname found - return classname, otherwise return null.
     * 
     * @param string $uri
     * 
     * @return string
     */
    public function matchURI($uri)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        
        if($path === false) {
            throw new \RuntimeException("Could not parse URI: '{$uri}'.");
        }

        return $this->matchPath($path);
    }

    /**
     * Matches path to classname.
     * 
     * If classname found - return classname, otherwise return null.
     * 
     * @param string $uri
     * 
     * @return string
     */
    public function matchPath($path)
    {
        $path = rtrim($path, "/");
        if($path == "") {
            $path = "/";
        }

        foreach($this->routes as $className => $info) {
            if(preg_match($info['matchPath'], $path) === 1) {
                return $className;
            }
        }
        
        return null;
    }

    /**
     * Matches Resource to class.
     * 
     * If classname found - return class instance, otherwise return null.
     * 
     * @param Request $request
     * 
     * @return Resource
     */
    public function matchResource(Request $request)
    {
        $className = $this->matchRequest($request);
        if($className === null) {
            throw new \RuntimeException("Could not map route to resource: '{$request->getRequestURI()}'.");
        }

        $resource = new $className($request, $this);

        return $resource;
    }

    /**
     * Matches Request to classname.
     * 
     * If classname found - return classname, otherwise return null.
     * 
     * @param Request $request
     * 
     * @return string
     */
    public function matchRequest(Request $request)
    {
        $path = parse_url($request->getRequestURI(), PHP_URL_PATH);
        
        if($path === false) {
            throw new \RuntimeException("Could not parse URI: '{$request->getRequestURI()}'.");
        }

        return $this->matchPath($path);
    }

    /**
     * Returns route data of specific Resource.
     *
     * If optional $requestMethod is given, method then returns routes only
     * for specific request method (GET, POST, DELETE, PUT).
     *
     * @param Resource $resource
     *
     * @return array
     */
    public function matchMethod(Resource $resource) {
        $className = get_class($resource);
        $listOfRoutes = $this->routes[$className];
        $requestMethod = $resource->getRequest()->getMethod();

        if(!array_key_exists($requestMethod, $listOfRoutes)) {
            throw new \RuntimeException("Could not match method '{$requestMethod}' on resource '{$className}'.");
        }
        $contentType = $resource->getRequest()->getContentType();
        if(($pos = strpos($contentType, ";")) > 0) {
            $contentType = substr($contentType, 0, $pos);
        }
        if($contentType === Resource::MULTIPART_FORM) {
            // a little hack so the end developers wouldn't need to specify
            // multipart/form-data explicitly (because in the end it is the same as
            // multipart/form-data-alternate
            $contentType = "multipart/form-data";
        }

        $listOfOptions = array();
        foreach($listOfRoutes[$requestMethod] as $route) {
            if($route['consumes'] === null) {
                $listOfOptions[] = $route;
            } elseif($contentType === null) {
                $listOfOptions[] = $route;
            } elseif(in_array($contentType, $route['consumes'])) {
                $listOfOptions[] = $route;
            }
        }

        $accept = explode(",", $resource->getRequest()->getHeader("HTTP_ACCEPT"));
        // remove q=N.N - priority marks, assuming that browser already sends Accept
        // header values sorted. Otherwise this will need some refactoring.
        array_walk($accept, function(&$item, $key) {
            if(($pos = strpos($item, ";q=")) > 0) {
                $item = substr($item, 0, $pos);
            }
            $item = trim($item);
        });

        foreach($accept as $browserHeader) {
            $regex = str_replace("/", "\/", $browserHeader);
            $regex = str_replace("+", "\+", $regex);
            $regex = "/" . str_replace("*", "[^\/]+", $regex) . "/i";
            foreach($listOfOptions as $option) {
                if($option['produces'] === null) {
                    // consider 'produces' => null as the default option, if found.
                    return $option['methodName'];
                }

                foreach($option['produces'] as $produces) {
                    if(preg_match($regex, $produces) === 1) {
                        return $option['methodName'];
                    }
                }
            }
        }

        // if we haven't found anything... start complaining
        return null;
    }

    /**
     * Builds URI to resource.
     * 
     * If classname is not found in route map - throws an exception.
     * 
     * @param Resource|string $resource
     * @param array $params
     * 
     * @return string
     */
    public function buildURI($resource, array $params = array())
    {
        if(is_object($resource)) {
            $resource = get_class($resource);
        }
        
        if(! array_key_exists($resource, $this->routes)) {
            throw new \RuntimeException("Could not find route for resource: '{$resource}'.");
        }
        
        $uri = array_slice(explode("/", $this->routes[$resource]['buildPath']), 1);
        foreach($uri as $idx => $segment) {
            if(empty($segment) || $segment[0] !== "{")
                continue;
            
            $segment = trim($segment, " {}");
            
            if(! array_key_exists($segment, $params)) {
                throw new \RuntimeException("Missing parameter '{$segment}' when generating route for resource: '{$resource}'.");
            }
            
            $uri[$idx] = urlencode($params[$segment]);
        }
        
        $uri = "/" . implode("/", $uri);
        if(preg_match($this->routes[$resource]['matchPath'], $uri) === 0) {
            throw new \RuntimeException("Could not generate valid URI for resource '{$resource}': '{$uri}'.");
        }
        
        return $uri;
    }
}
