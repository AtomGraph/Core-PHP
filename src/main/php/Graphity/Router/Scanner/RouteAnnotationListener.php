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

namespace Graphity\Router\Scanner;

use Graphity\Util\Scanning\ScannerListener;

/**
 * Route annotation listener
 * 
 * Finds, scans and outputs array with found route (@Path) annotations.
 */
class RouteAnnotationListener implements ScannerListener
{

    /**
     * @var array
     */
    protected $listOfAnnotations = array();

    /**
     * @see ScannerListener::accept()
     */
    public function accept($path)
    {
        if(strtolower(substr($path, - 4, 4)) !== ".php") {
            return false;
        }
        
        if(strpos(strtolower(basename($path)), "resource") === false) {
            // Only filenames with "Resource" will be accepted.
            return false;
        }

        $className = basename($path, ".php");
        $namespace = str_ireplace("src/main/php", "", dirname($path));
        $fqName = str_ireplace("/", "\\", ltrim($namespace, "/")) . "\\" . $className;

        $reflection = new \ReflectionClass($fqName);
        if($reflection->isSubclassOf('Graphity\\Resource') === false) {
            return false;
        }
        
        return true;
    }

    /**
     * @see ScannerListener::process()
     */
    public function process($path)
    {
        $className = basename($path, ".php");
        $namespace = str_ireplace("src/main/php", "", dirname($path));

        $fqName = str_ireplace("/", "\\", ltrim($namespace, "/")) . "\\" . $className;

        $reflection = new \ReflectionAnnotatedClass($fqName);
        $listOfMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $path = $reflection->getAnnotation('Path');

        $result = array();
        foreach($listOfMethods as $method) {
            $item = array();

            $listOfAnnotations = $method->getAllAnnotations();
            foreach($listOfAnnotations as $annotation) {
                if(in_array(strtoupper(get_class($annotation)), array(
                    "GET",
                    "POST",
                    "DELETE",
                    "PUT"))) {
                    $item['requestMethod'] = get_class($annotation);
                } else if(get_class($annotation) === "Consumes") {
                    if(empty($item['consumes'])) {
                        $item['consumes'] = array();
                    }
                    $item['consumes'][] = $annotation->value;
                } else if(get_class($annotation) === "Produces") {
                    if(empty($item['produces'])) {
                        $item['produces'] = array();
                    }
                    $item['produces'][] = $annotation->value;
                }
            }

            if(!array_key_exists('requestMethod', $item)) {
                continue;
            }

            $item['classMethod'] = $method->name;

            $result[] = $item;
        }

        $this->listOfAnnotations[] = array(
            'className' => str_ireplace("\\", "\\\\", $fqName),
            'path' => $path,
            'items'=> $result
        );
    }

    /**
     * Returns list of found path annotations.
     * 
     * @return array
     */
    public function getAnnotations()
    {
        return $this->listOfAnnotations;
    }
}
