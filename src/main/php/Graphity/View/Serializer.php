<?php
/**
 * Serializes objects into XML strings.
 * It is an abstract class for sub-classing. An application Serializer should extend the framework Serializer and call the parent::serialize().
 *
 * @package		graphity
 * @author		Martynas Jusevicius <pumba@xml.lt>
 * @link		http://www.xml.lt
 */

namespace HeltNormalt\View;

abstract class Serializer
{

    /**
     * Serializes objects into XML strings.
     * @param mixed $mixed Object to serialize
     * @return string XML string
     */
    
    public static function serialize($mixed)
    {
        $xml = null;
        
        //if (is_array($mixed)) return self::serializeArray($mixed);
        

        if($mixed instanceof Resource)
            return self::serializeResource($mixed);
    }

    /**
     * Serializes Resource objects into XML strings.
     * @param Resource $resource Resource object to serialize
     * @return string XML string
     */
    
    protected static function serializeResource(Resource $resource)
    {
        return "<" . get_class($resource) . " uri=\"" . $resource->getURI() . "\"/>";
    }

}

