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

//temporary:
//namespace Graphity\Router\Annotation;

/**
 * Path annotation.
 * 
 * Half-compliant with JAX-RS routes (does not support Path annotations on methods).
 *
 * @Target("class")
 */
class Path extends \Annotation
{

    const DEFAULT_PATTERN = "[^\/]+";

    /**
     * Returns absolute path used to build URI.
     * 
     * @return string
     */
    public function getBuildPath()
    {
        $listOfSegments = array_slice(explode("/", rtrim($this->value, "/")), 1, null, false);
        foreach($listOfSegments as $idx => $segment) {
            if(strpos($segment, "{") !== 0) {
                continue;
            }
            
            if(strpos($segment, ":") === false) {
                continue;
            }
            
            $name = explode(":", $segment, 2);
            $name = trim($name[0], " {}");
            $listOfSegments[$idx] = "{" . $name . "}";
        }
        
        return '/' . implode("/", $listOfSegments);
    }

    /**
     * Returns absolute path regexp used to match URI.
     * 
     * @return string
     */
    public function getMatchPath()
    {
        $listOfSegments = array_slice(explode("/", rtrim($this->value, "/")), 1, null, false);
        foreach($listOfSegments as $idx => $segment) {
            if(strpos($segment, "{") !== 0) {
                continue;
            }
            
            if(strpos($segment, ":") === false) {
                $listOfSegments[$idx] = "(?<" . trim($segment, " {}") . ">" . static::DEFAULT_PATTERN . ")";
                continue;
            }
            
            $listOfParts = explode(":", $segment, 2);
            $name = trim($listOfParts[0], " {");
            $pattern = trim($listOfParts[1], " ");
            if($pattern[strlen($pattern) - 1] == "}") {
                $pattern = substr($pattern, 0, strlen($pattern) - 1);
            }
            $listOfSegments[$idx] = "(?<" . $name . ">" . str_replace("\\\\", "\\", $pattern) . ")";
        }
        
        return '^\/' . implode("\/", $listOfSegments) . '$';
    }
    
    /**
     * Returns number of segments in URI
     * 
     * @return integer
     */
    public function getSegmentCount()
    {
        $listOfSegments = array_slice(explode("/", rtrim($this->value, "/")), 1, null, false);
        return count($listOfSegments);
    }
    
    /**
     * Returns number of parameters in URI
     * 
     * @return integer
     */
    public function getParameterCount()
    {
        $count = 0;
        $listOfSegments = array_slice(explode("/", rtrim($this->value, "/")), 1, null, false);
        foreach($listOfSegments as $idx => $segment) {
            if($segment[0] === "{") {
                $count++;
            }
        }
        
        return $count;
    }
}
