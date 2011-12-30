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

    /**
     * Returns weight of the path.
     *
     * The more specific path, the more "weight" it gets.
     *
     * @return integer
     */
    public function getWeight()
    {
        // NOTE: these are handcrafted, so might not work in all cases,
        // if you have an idea for improvement - please share.

        // number of segments has the most weight
        $weight = $this->getSegmentCount() * 4;
        // number of static segments (segments that are not parameters) has a little bit less weight
        $weight += ($this->getSegmentCount() - $this->getParameterCount()) * 2;
        /* the more specific regexp, the more weight:
            e.g.: \w or \d is more specific than \w+ or \d+
            and \w+ and \d+ are more specific than \w* ir \d*
            and \w* and \d* are more specific than .*
        */
        $listOfSegments = array_slice(explode("/", rtrim($this->value, "/")), 1, null, false);
        foreach($listOfSegments as $idx => $segment) {
            if($segment[0] === "{") {
                if(($pos = strpos($segment, ":")) !== false) {
                    $regex = trim(substr($segment, $pos + 1, -1));
                    if(strpos($segment, ".*") !== false) {
                        continue;
                    } elseif(strpos($segment, "\w*") !== false || strpos($segment, "\d*") !== false) {
                        $weight += 1;
                    } elseif(strpos($segment, "\w+") !== false || strpos($segment, "\d+") !== false) {
                        $weight += 2;
                    } elseif(strpos($segment, "\w") !== false || strpos($segment, "\d") !== false) {
                        $weight += 3;
                    }
                }
            }
        }

        return $weight;
    }
}
