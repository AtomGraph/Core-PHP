<?php
/**
 * Path annotation.
 * 
 * Half-compliant with JAX-RS routes (does not support Path annotations on methods).
 * 
 * @author Julius Šėporaitis <julius@seporaitis.net>
 */

//namespace Graphity\Router\Annotation;

/* 
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
