<?php
/**
 * Route annotation listener
 * 
 * Finds, scans and outputs array with found route (@Path) annotations.
 * 
 * @author Julius Šėporaitis <julius@seporaitis.net>
 */

namespace Graphity;

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
        
        return true;
    }

    /**
     * @see ScannerListener::process()
     */
    public function process($path)
    {
        $className = basename($path, ".php");
        
        $reflection = new \ReflectionAnnotatedClass($className);
        $listOfMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        $path = $reflection->getAnnotation('Path');

        $result = array();
        foreach($listOfMethods as $method) {
            $item = array();

            $listOfAnnotations = $method->getAllAnnotations();
            foreach($listOfAnnotations as $annotation) {
                if($annotation instanceof GET) {
                    $item['requestMethod'] = "GET";
                } else if($annotation instanceof POST) {
                    $item['requestMethod'] = "POST";
                } else if($annotation instanceof DELETE) {
                    $item['requestMethod'] = "DELETE";
                } else if($annotation instanceof PUT) {
                    $item['requestMethod'] = "PUT";
                } else if($annotation instanceof Consumes) {
                    if(empty($item['consumes'])) {
                        $item['consumes'] = array();
                    }
                    $item['consumes'][] = $annotation->value;
                } else if($annotation instanceof Produces) {
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
            'className' => $className,
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
