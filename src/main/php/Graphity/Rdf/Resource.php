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
 * An RDF resource.
 * 
 * Based on Jena: http://jena.sourceforge.net/javadoc/com/hp/hpl/jena/rdf/model/RDFResource.html
 */
class Resource implements Node
{

    /**
     * @var string Absolute resource URI or anonymous node ID (starts with "_:")
     */
    protected $uri = null;
    
    /**
     * Create a resource.
     * 
     * Set $uri to null for a blank node (bnode).
     * 
     * @param string $uri
     */
    public function __construct($uri = null) {
        if($uri == null) {
            $uri = "_:" . uniqid();
        }
        $this->uri = $uri;
    }

    /**
     * Return URI of the resource, or null if it's a bnode
     * 
     * @return string
     */
    public function getURI()
    {
        if($this->isAnonymous()) {
            return null;
        }
        return $this->uri;
    }
    
    /**
     * Return true iff this RDFResource is a URI resource with the given URI.
     * 
     * @return boolean
     */
    public function hasURI($uri)
    {
        return $this->isURIResource() && ($this->uri == $uri);
    }
    
    /**
     * Return anonymous id or null if it's not a bnode
     * 
     * @return string
     */
    public function getAnonymousId() {
        if($this->isAnonymous()) {
            return substr($this->uri, strlen("_:"));
        }
        return null;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isAnonymous()
     */
    public function isAnonymous()
    {
        return strpos($this->uri, "_:") === 0;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isLiteral()
     */
    public function isLiteral()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isResource()
     */
    public function isResource()
    {
        return true;
    }

    /* (non-PHPdoc)
     * @see RDFNode::isURIResource()
     */
    public function isURIResource()
    {
        return ($this->isResource() && !$this->isAnonymous());
    }

    /* (non-PHPdoc)
     * @see RDFNode::__toString()
     */
    public function __toString()
    {
        return $this->isAnonymous() ? $this->uri : sprintf("<%s>", $this->uri);
    }

}
