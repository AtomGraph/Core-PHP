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
        
        return '/' . rtrim(implode("/", $listOfSegments), "/");
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
        
        return '^\/' . rtrim(implode("\/", $listOfSegments), "/") . '(\\/.*)?';
    }
    
    /**
     * Return the number of literal characters.
     *
     * Literal characters means those not resulting from template variable substitution.
     *
     * @return integer
     */
    public function getLiteralCharacterCount()
    {
        if($this->getBuildPath() === "/") {
            return 0;
        }

        $listOfSegments = explode("/", trim($this->getBuildPath(), "/"));
        $count = 0;

        foreach($listOfSegments as $segment) {
            if($segment[0] != "{") {
                $count += strlen($segment);
            }
        }

        return $count;
    }

    /**
     * Return the number of capturing groups (parameters).
     *
     * @param boolean $includeDefault   Should the count include default capturing group (self::DEFAULT_PATTERN)?
     *
     * @return integer
     */

    public function getParameterCount($includeDefault = true) {
        $listOfSegments = array_slice(explode("/", rtrim($this->value, "/")), 1, null, false);
        $count = 0;

        foreach($listOfSegments as $segment) {
            if(strpos($segment, "{") !== 0) {
                continue;
            }
            
            if($includeDefault === false) {
                if(strpos($segment, ":") !== false) {
                    $count++;
                }
            } else {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Compare this Path pattern to another path pattern.
     *
     * According to JAX-RS (3.7.2 1.e):
     *  1. Number of literal characters, descending.
     *  2. Number of capturing groups, descending.
     *  3. Number of capturing groups with non-default regexes, descending.
     *
     * @param Path $other   Path to compare to.
     *
     * @return integer
     */
    public function compare(Path $other) {
        $thisCount = $this->getLiteralCharacterCount();
        $otherCount = $other->getLiteralCharacterCount();

        if($thisCount > $otherCount) {
            return -1;
        } elseif($thisCount < $otherCount) {
            return 1;
        } else {
            $thisCount = $this->getParameterCount();
            $otherCount = $other->getParameterCount();
            if($thisCount > $otherCount) {
                return -1;
            } elseif($thisCount < $otherCount) {
                return 1;
            } else {
                $thisCount = $this->getParameterCount(false);
                $otherCount = $other->getParameterCount(false);
                if($thisCount > $otherCount) {
                    return -1;
                } elseif($thisCount < $otherCount) {
                    return 1;
                }

                return 0;
            }
        }
    }
}

