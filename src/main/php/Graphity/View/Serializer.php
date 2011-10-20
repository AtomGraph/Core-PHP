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
 *  @author         Martynas Jusevičius <pumba@xml.lt>
 *  @link           http://graphity.org/
 */

namespace HeltNormalt\View;

/**
 * Serializes objects into XML strings.
 * It is an abstract class for sub-classing. An application Serializer should extend the framework Serializer and call the parent::serialize().
 */
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

