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

namespace Graphity\Rdf;

/**
 * Interface covering RDF resources and literals.
 * 
 * Based on Jena: http://jena.sourceforge.net/javadoc/com/hp/hpl/jena/rdf/model/RDFNode.html
 */
interface Node
{
    /**
     * Answer true iff this RDFNode is an anonymous resource.
     * 
     * @return boolean
     */
    public function isAnonymous();
    
    /**
     * Answer true iff this RDFNode is a Literal
     * 
     * @return boolean
     */
    public function isLiteral();
    
    /**
     * Answer true iff this RDFNode is a URI resource or an anonymous resource (ie is not literal).
     * 
     * @return boolean
     */
    public function isResource();
    
    /**
     * Answer true iff this RDFNode is a named resource (ie not anonymous resource and not literal).
     * 
     * @return boolean
     */
    public function isURIResource();
    
    /**
     * Return a string representation of the node.
     * 
     * @return string
     */
    public function __toString();
}
